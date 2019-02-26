<?php

namespace App\Http\Handlers\DocumentBuilder;

class ResumeWordBuilder extends EssentialPhpWordBuilder{

    private $bullet_start = '<w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr><w:spacing w:after="0" w:line="360" w:lineRule="auto"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Helvetica" w:hAnsi="Helvetica" w:cs="Helvetica"/><w:sz w:val="18"/></w:rPr><w:t>';
    private $bullet_end = '</w:t></w:r></w:p>';
    private $_parser;
    private $result = '';
    
    public function __construct() {
        $this->_parser = new \HTMLtoOpenXML\Parser();
    }

    public function getResidency(){
        return $this->residency;
    }
    
    public function setResidency($residency){
        foreach($residency as $row){
            $arr[] = '<b>'.$row->country.'</b> '.$row->visa_status;
        }
        $this->residency = $arr;
    }
    
    public function showResidency($residency){
        $residency_arr = '';
        foreach($residency as $row){
            $residency_arr .= $row.' | ';
        }
        $x = rtrim($residency_arr,' | ');
        return str_replace('/</w:t>','</w:t>',$this->_parser->fromHTML($x));
    }    
    
    public function getRowCoreCompetencies(){
        if($this->getCoreCompetencies()){
            foreach($this->getCoreCompetencies() as $row){
                $this->result .= $this->bullet_start.$row.$this->bullet_end;
            }
            return $this->result;
        }
    }
    
    public function getCloneRow($templateProcessor,$placeholder,$number,$value){
        $templateProcessor->cloneRow($placeholder,$number);
        for($a=0;$a<$number;$a++){
            $templateProcessor->setValue($placeholder.'#'.($a+1), $value[$a]);
        }
    }
    
    public function getSetValueClone($templateProcessor,$placeholder,$number,$value){
        for($a=0;$a<$number;$a++){
            $templateProcessor->setValue($placeholder.'#'.($a+1), $value[$a]);
        }
    }

    public function setValueBlock($templateProcessor,$value,$total){
        $x = 0;
        while($x < $total){
            foreach($value[$x] as $key => $result){
                if($key=='summary'){
                    $templateProcessor->setValue($key,$this->_parser->fromHTML($result),1);
                } else if($key=='logo' && $result!=''){
                    $templateProcessor->setImg($key,array('src' => $result,'swh'=>'200', 'size'=>array(0=>50, 1=>50, 2=>20)));
                } else {
                    $templateProcessor->setValue($key,htmlentities($result),1);
                }
            }
            $x++;
        }
    }
    
    public function sameCompanyWorkExperience($templateProcessor,$workExperience){
        $count = 0;
        $oldCompany = '';
        $templateProcessor->cloneBlock('workExperience',3);
//        $templateProcessor->setValue('workCompany','<w:p><w:r><w:t>testing testing</w:t></w:r></w:p>');
        $templateProcessor->setValue('workCompany','testing testing');
        
//        foreach($workExperience as $row){
//            if($row->workCompany != $oldCompany){
//                $oldCompany = $row->workCompany;
//                $count++;
//                $templateProcessor->cloneBlock('workExperience',1);
////                // clone block
//            } else {
//                $templateProcessor->replaceBlock('workExperience','<w:p><w:r><w:t>testing testing</w:t></w:r></w:p>');
//                $templateProcessor->cloneBlock('workSameCompany',1);
                // add tag for same company
//            }
//        }
//        return $count;
    }
    
    public function runTemplateReplacement($replacement_arr,$replacement_editor,$FileLocation){
        $templateProcessor = new EssentialTemplateProcessor($FileLocation);
//        $this->sameCompanyWorkExperience($templateProcessor, $this->getWorkExperience());
//        var_dump(count($this->getEducation()));exit();
        ($this->getYearsOfExperience() > 5) ? $templateProcessor->deleteBlock('entryLevelSummary') : $templateProcessor->deleteBlock('professionalSummary');
        $templateProcessor->setValue('visaMe',$this->showResidency($this->getResidency()));
        $templateProcessor->setImg('profileImg',array('src' => $this->getProfileImg(),'swh'=>'200', 'size'=>array(0=>125, 1=>125, 2=>20)));
        $this->getCloneRow($templateProcessor,'preparedBy',count($this->getPreparedBy()),$this->getPreparedBy());
        $this->getCloneRow($templateProcessor,'nameSkills',count($this->getSkillsArr()),$this->getSkillsArr());
        $this->getCloneRow($templateProcessor,'language',count($this->getLanguage()),$this->getLanguage());
        $this->clearCloneTag($templateProcessor,'Work Experience','workExperience',count($this->getWorkExperience()));
        $this->clearCloneTag($templateProcessor,'Achievements','achievement',0);
        $this->clearCloneTag($templateProcessor,'Education','education',count($this->getEducation()));
        $this->clearCloneTag($templateProcessor,'References','references',count($this->getReferences()));
        $this->clearCloneTag($templateProcessor,'Professional Certificate','profCertificate',count($this->getProfCertificate()));
        $this->clearCloneTag($templateProcessor,'Training Certificate','trainingCertificate',count($this->getTrainingCertificate()));
        $this->getSetValueClone($templateProcessor,'preparedTitle',count($this->getPreparedByTitle()),$this->getPreparedByTitle());
        $this->getSetValueClone($templateProcessor,'levelSkills',count($this->getSkillsArrTitle()),$this->getSkillsArrTitle());
        $this->getSetValueClone($templateProcessor,'languageWritten',count($this->getLanguageSpoken()),$this->getLanguageSpoken());
        $this->getSetValueClone($templateProcessor,'languageSpoken',count($this->getLanguageWritten()),$this->getLanguageWritten());
        $this->setValueBlock($templateProcessor,$this->getWorkExperience(),count($this->getWorkExperience()));
        $this->setValueBlock($templateProcessor,$this->getAchievement(),count($this->getAchievement()));
        $this->setValueBlock($templateProcessor,$this->getEducation(),count($this->getEducation()));
        $this->setValueBlock($templateProcessor,$this->getReferences(),count($this->getReferences()));
        $this->setValueBlock($templateProcessor,$this->getProfCertificate(),count($this->getProfCertificate()));
        $this->setValueBlock($templateProcessor,$this->getTrainingCertificate(),count($this->getTrainingCertificate()));

        foreach($replacement_arr as $key => $value){
            if($key && $value){
                $templateProcessor->replaceBlock($key,$value);
                $templateProcessor->setValue($key,$value);
            }
        }
        
        foreach($replacement_editor as $key_editor => $value_editor){
            if($key_editor && $value_editor){
                $templateProcessor->replaceBlock($key_editor,$this->_parser->fromHTML($value_editor));
                $templateProcessor->setValue($key_editor,$this->_parser->fromHTML($value_editor),1);
            }
        }
        return $templateProcessor;
    }

}
