<?php

namespace App\Http\Controllers;
use App\Http\Handlers\DocumentBuilder\DocumentOutputInterface;
use App\Http\Handlers\DocumentBuilder\ResumeWordBuilder;
use App\Http\Handlers\DocumentBuilder\EssentialOnlyOffice;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use App\Http\Handlers\DocumentBuilder\EssentialTemplateProcessor;

class DocumentController extends Controller{
    
    public function index(){
        $this->templ->setLocation('../resources');
        $this->templ->setFilename('Resume_1.docx');
        $this->templ->setFileResult('Resume_1.pdf');
        $file_location = $this->templ->getLocation().'/'.$this->templ->getFilename();
        $file_result = $this->templ->getLocation().'/'.$this->templ->getFileResult();
        print_r($this->templ->getPlaceholders($file_location,$file_result));
    }

    public function runResumeTest(){
        $profile_json = json_decode(Storage::disk('local')->get('candidate_profile.json'));
        $word = new ResumeWordBuilder();
        $word->setTemplateFileLocation('http://projects.recruiterpal.test/documents/Resume_1.docx');
        $word->setOutputFileLocation(Storage::disk('local')->getAdapter()->getPathPrefix().'/public/Result_Resume_1.docx');
//        $word->setOutputFileLocation('http://projects.recruiterpal.test/documents/Result_Resume_123.docx');
        $word->setOutputFormat(\Config::get('constant.OutputFormat.PDF'));
        $word->setPreparedBy($word->getValueJson($profile_json->prepared_by, 'content'));
        $word->setPreparedByTitle($word->getValueJson($profile_json->prepared_by, 'title'));
        $word->setSkillsArr($word->getValueJson($profile_json->skills, 'skill'));
        $word->setSkillsArrTitle($word->getValueJson($profile_json->skills, 'proficiency_name'));
        $word->setResidency($profile_json->residency);
        $word->setLanguage($word->getValueJson($profile_json->languages, 'language'));
        $word->setLanguageWritten($word->getValueJson($profile_json->languages, 'written_proficiency'));
        $word->setLanguageSpoken($word->getValueJson($profile_json->languages, 'spoken_proficiency'));
        $word->setWorkExperience($profile_json->work_experience);
        $word->setCoreCompetencies($profile_json->core_competencies);
        $word->setEducation($profile_json->education);
        $word->setAchievement($profile_json->achievements);
        $word->setReferences($profile_json->referee);
        $word->setNationalServices($profile_json->national_service);
        $word->setYearsOfExperience($profile_json->other_details->years_of_experience);
        $word->setProfileImg($profile_json->profile_overview->thumbnail);
        $word->setProfCertificate($profile_json->professional_certificates);
        $word->setTrainingCertificate($profile_json->training_certificates);
        
        $replacement_arr = [
            'fullName'          => $profile_json->profile_overview->full_name,
            'presentJobTitle'   => $profile_json->work_experience[0]->job_title,
            'presentCompany'    => $profile_json->work_experience[0]->workCompany,
            'presentCity'       => $profile_json->address_details->current_city_of_residence_id,
            'presentCountry'    => $profile_json->address_details->current_country_of_residence,
            'jobRole'           => $profile_json->work_experience[0]->job_role,
            'lastCompany'       => $profile_json->work_experience[0]->workCompany,
            'lastUniversity'    => $profile_json->education[0]->institution,
            'phoneMe'           => $profile_json->profile_overview->full_contact_number,
            'emailMe'           => $profile_json->email_details[0]->email,
//            'visaMe'            => $word->getResidency(),
            'lastSalary'        => $profile_json->profile_summary->salary_availability->last_drawn_salary,
            'askingSalary'      => $profile_json->profile_summary->salary_availability->expected_salary,
            'noticePeriod'      => $profile_json->profile_summary->salary_availability->notice_period,
            'gender'            => $profile_json->profile_overview->gender,
            'dateBirth'         => $profile_json->profile_overview->client_dob_text,
            'ageYears'          => $profile_json->profile_overview->age,
            'address'           => $profile_json->address_details->current_address,
            'coreCompetencies'  => $word->getRowCoreCompetencies(),
            'nationalService'   => $profile_json->national_service[0]->nationalService,
            'nationalServiceDesc'=> $profile_json->national_service[0]->nationalServiceDesc,
            'nationalServiceDate'=> $profile_json->national_service[0]->nationalServiceDate
        ];
        //this one for replace value with HTML tag
        $replacement_editor = [
            'overallAssessment' => $profile_json->overall_assessment,
            'salaryNotes'       => $profile_json->profile_summary->salary_availability->salary_info,
            'careerGoals'       => $profile_json->career_objective
        ];
        $word->setReplacementArr($replacement_arr);
        $word->setReplacementEditor($replacement_editor);
        $templateProcessor = $word->runTemplateReplacement($word->getReplacementArr(),$word->getReplacementEditor(),$word->getTemplateFileLocation());
        preg_match_all('/\$\{\/[a-zA-Z0-9}]+/i', $templateProcessor->tempDocumentMainPart, $matches);
        foreach($matches[0] as $data){
            $templateProcessor->setValue($data, '');
        }
        $word->runResumeReplacement($templateProcessor);
    }
    
    public function addImage(){
        $fileName = "../resources/helloWorld1.docx";
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileName);
        $sections = $phpWord->getSections();
        $section = $sections[0];
        $arrays = $section->getElements();
        var_dump($sections);
        exit();
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        // Begin code
        $section = $phpWord->addSection();
        $section->addText('Local image without any styles:');
        $section->addImage('../resources/logo.jpg');
        $this->printSeparator($section);
        $section->addText('Local image with styles:');
        $section->addImage('../resources/logo.jpg', array('width' => 210, 'height' => 210, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
        // Remote image
        $this->printSeparator($section);
        $source = 'http://php.net/images/logos/php-med-trans-light.gif';
        $section->addText("Remote image from: {$source}");
        $section->addImage($source);
        // Image from string
        $this->printSeparator($section);
        $source = '../resources/logo.jpg';
        $fileContent = file_get_contents($source);
        $section->addText('Image from string');
        $section->addImage($fileContent);
        //Wrapping style
        $this->printSeparator($section);
        $text = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. ', 2);
        $wrappingStyles = array('inline', 'behind', 'infront', 'square', 'tight');
        foreach ($wrappingStyles as $wrappingStyle) {
            $section->addText("Wrapping style {$wrappingStyle}");
            $section->addImage(
                '../resources/logo.jpg',
                array(
                    'positioning'        => 'relative',
                    'marginTop'          => -1,
                    'marginLeft'         => 1,
                    'width'              => 80,
                    'height'             => 80,
                    'wrappingStyle'      => $wrappingStyle,
                    'wrapDistanceRight'  => Converter::cmToPoint(1),
                    'wrapDistanceBottom' => Converter::cmToPoint(1),
                )
            );
            $section->addText($text);
            $this->printSeparator($section);
        }
        //Absolute positioning
        $section->addText('Absolute positioning: see top right corner of page');
        $section->addImage(
            '../resources/logo.jpg',
            array(
                'width'            => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(3),
                'height'           => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(3),
                'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
                'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'marginLeft'       => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(15.5),
                'marginTop'        => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(1.55),
            )
        );

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('../resources/helloWorld.docx');
    }
    
    function printSeparator(Section $section)
    {
        $section->addTextBreak();
        $lineStyle = array('weight' => 0.2, 'width' => 150, 'height' => 0, 'align' => 'center');
        $section->addLine($lineStyle);
        $section->addTextBreak(2);
    }

//    don't move this one, for routing purpose
    public function getFileUrl($filename)
    {
        $path = storage_path('app/public/').$filename;
        return response()->download($path);
    }
    
    public function onlyOffice(){
        $date = new \DateTime();
        $word = new ResumeWordBuilder();
        $word->setTemplateFileLocation('http://projects.recruiterpal.test/documents/Result_Resume_1.docx');
        $word->setOutputFileLocation('http://projects.recruiterpal.test/documents/');
        return view('document',[
            'documentId'=> 'bmw78-'.$date->getTimestamp(),
            'fileUrl'   => $word->getTemplateFileLocation(),
            'callBack'  => 'http://projects.recruiterpal.test/callBack'
        ]);
    }

    public function callBack(){
        $onlyoffice = new EssentialOnlyOffice();
        $onlyoffice->saveDocumentCallback(request()->all());
    }


}
