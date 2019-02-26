<?php

namespace App\Http\Handlers\DocumentBuilder;

class EssentialPhpWordBuilder{

//    public function __get($property) {
//        if(property_exists($this, $property)) {
//            return $this->$property;
//        }
//    }
//
//    public function __set($property, $value) {
//        $this->$property = $value;
//        return $this;
//    }
    public function setTemplateFileLocation($templateFileLocation){
        $this->template_file_location = $templateFileLocation;
        return $this;
    }
    
    public function getTemplateFileLocation(){
        return $this->template_file_location;
    }
    
    public function setOutputFileLocation($outputFileLocation){
        $this->output_file_location = $outputFileLocation;
        return $this;
    }
    
    public function getOutputFileLocation(){
        return $this->output_file_location;
    }
    
    public function setOutputFormat($outputFormat){
        $this->output_format = $outputFormat;
        return $this;
    }
    
    public function getOutputFormat(){
        return $this->output_format;
    }
    
    public function setPreparedBy($preparedBy){
        $this->prepared_by = $preparedBy;
        return $this;
    }
    
    public function getPreparedBy(){
        return $this->prepared_by;
    }
    
    public function setPreparedByTitle($preparedByTitle){
        $this->prepared_by_title = $preparedByTitle;
        return $this;
    }
    
    public function getPreparedByTitle(){
        return $this->prepared_by_title;
    }
    
    public function setSkillsArr($skillsArr){
        $this->skills_arr = $skillsArr;
        return $this;
    }
    
    public function getSkillsArr(){
        return $this->skills_arr;
    }
    
    public function setSkillsArrTitle($skillsArrTitle){
        $this->skills_arr_title = $skillsArrTitle;
        return $this;
    }
    
    public function getSkillsArrTitle(){
        return $this->skills_arr_title;
    }
    
    public function setLanguage($language){
        $this->language = $language;
        return $this;
    }
    
    public function getLanguage(){
        return $this->language;
    }
    
    public function setLanguageWritten($language_written){
        $this->language_written = $language_written;
        return $this;
    }
    
    public function getLanguageWritten(){
        return $this->language_written;
    }
    
    public function setLanguageSpoken($language_spoken){
        $this->language_spoken = $language_spoken;
        return $this;
    }
    
    public function getLanguageSpoken(){
        return $this->language_spoken;
    }
    
    public function setWorkExperience($work_experience){
        $this->work_experience = $work_experience;
        return $this;
    }
    
    public function getWorkExperience(){
        return $this->work_experience;
    }
    
    public function setCoreCompetencies($core_competencies){
        $this->core_competencies = $core_competencies;
        return $this;
    }
    
    public function getCoreCompetencies(){
        return $this->core_competencies;
    }
    
    public function setEducation($education){
        $this->education = $education;
        return $this;
    }
    
    public function getEducation(){
        return $this->education;
    }
    
    public function setAchievement($achievement){
        $this->achievement = $achievement;
        return $this;
    }
    
    public function getAchievement(){
        return $this->achievement;
    }
    
    public function setReferences($references){
        $this->references = $references;
        return $this;
    }
    
    public function getReferences(){
        return $this->references;
    }
    
    public function setNationalServices($national_services){
        $this->national_services = $national_services;
        return $this;
    }
    
    public function getNationalServices(){
        return $this->national_services;
    }





    public function setProfCertificate($prof_certificate){
        $this->prof_certificate = $prof_certificate;
        return $this;
    }

    public function getProfCertificate(){
        return $this->prof_certificate;
    }

    public function setTrainingCertificate($training_certificate){
        $this->training_certificate = $training_certificate;
        return $this;
    }

    public function getTrainingCertificate(){
        return $this->training_certificate;
    }





    public function setYearsOfExperience($years_of_experience){
        $this->years_of_experience = $years_of_experience;
        return $this;
    }
    
    public function getYearsOfExperience(){
        return $this->years_of_experience;
    }
    
    public function setReplacementArr($replacement_arr){
        $this->replacement_arr = $replacement_arr;
        return $this;
    }
    
    public function getReplacementArr(){
        return $this->replacement_arr;
    }
    
    public function setReplacementEditor($replacement_editor){
        $this->replacement_editor = $replacement_editor;
        return $this;
    }
    
    public function getReplacementEditor(){
        return $this->replacement_editor;
    }
    
    public function setProfileImg($profile_img){
        $this->profile_img = $profile_img;
        return $this;
    }
    
    public function getProfileImg(){
        return $this->profile_img;
    }
    
    public function getValueJson($obj,$column){
        foreach($obj as $key => $row){
            $result[] = $row->$column;
        }
        return $result;
    }
    
    public function runResumeReplacement($templateProcessor){
        var_dump($templateProcessor);
        $templateProcessor->saveAs($this->getOutputFileLocation());
    }

    public function clearCloneTag($templateProcessor,$title,$tag,$total){
        $titleTag = $tag.'Title';
        if($total > 0){
            $templateProcessor->setValue($titleTag,$title);
            $templateProcessor->cloneBlockString($tag,$total);
        } else {
            $templateProcessor->setValue($titleTag,'');
            $templateProcessor->deleteBlock($tag);
        }
    }

}