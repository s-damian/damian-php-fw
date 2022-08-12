<?php

namespace DamianPhp\Validation;

use DamianPhp\Support\Helper;
use DamianPhp\Support\Facades\Input;
use DamianPhp\Support\Facades\Security;
use DamianPhp\Contracts\Validation\ValidatorInterface;

/**
 * Pour validation des fichiés uploadés.
 * 
 * @author  Stephen Damian <contact@devandweb.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class FileValidation
{
    private ValidatorInterface $validator;

	public function __construct(ValidatorInterface $validator)
	{
		$this->validator = $validator;
	}

    /**
     * Upload unique.
     */
    public function verifyOneFile(string $input, array $value): void
    {
        $error = '';

        $fileName = Input::file($input)['name']; // nom du fichier
        $fileSize = Input::file($input)['size']; // poid du fichier

        // ***** nom - format *****
        if (isset($value['format_file'])) {
            if (preg_match(Validator::REGEX_CHARACTERS_PROHIBITED_NAME_FILE, $fileName)) {
                $error .= '<i style="font-size:16px;">- "'.$fileName.'" : '.$this->pushErrorWithFile('format_name').'</i><br>';
            }
        }
        // ***** /nom - format *****

        // ***** nom pas déjà pris ***** 
        if (isset($value['name_not_taken'])) {
            $listRep = scandir($value['path']);
            if (in_array($fileName, $listRep)) {
                $error .= '<i style="font-size:16px;">- "'.$fileName.'" : '.$this->pushErrorWithFile('name_not_taken').'</i><br>';
            }
        }
        // ***** /nom pas déjà pris *****    

        // ***** nom - length *****       
        if (isset($value['max_length_name'])) {
            if (mb_strlen($fileName) > $value['max_length_name']) {
                $error .= '<i style="font-size:16px;">- "'.$fileName.'" : '.$this->pushErrorWithFile('max_length_name', $value).'</i><br>';
            }
        } elseif (isset($value['specific_name'])) {
            if ($fileName !== $value['specific_name']) {
                $error .= '<i style="font-size:16px;">- "'.$fileName.'" : '.$this->pushErrorWithFile('specific_name', $value).'</i><br>';
            }
        }
        // ***** /nom - length *****

        // ***** poid *****
        if (isset($value['max_size'])) {
            if ($fileSize > $value['max_size']) {
                $error .= '<i style="font-size:16px;">- "'.$fileName.'" : '.$this->pushErrorWithFile('max_size', $value).'</i><br>';
            }
        }
        // ***** /poid *****

        // ***** extension *****
        if ($value['extension'] === 'image') {
            $extension = Security::extsImgValid();
            $extMessage = $this->pushErrorWithFile('extension_image');
        } elseif ($value['extension'] === 'audio') {
            $extension = Security::extsAudioValid();
            $extMessage = $this->pushErrorWithFile('extension_audio');
        } elseif ($value['extension'] === 'video') {
            $extension = Security::extsVideoValid();
            $extMessage = $this->pushErrorWithFile('extension_video');
        } elseif ($value['extension'] === 'doc') {
            $extension = Security::extsDocValid();
            $extMessage = $this->pushErrorWithFile('extension_doc');
        } elseif ($value['extension'] === 'file') {
            $extension = Security::extsFileValid();
            $extMessage = $this->pushErrorWithFile('extension_file');
        }

        if (!in_array(Security::getExtFile(strtolower($fileName)), $extension)) {
            $error .= '<i style="font-size:16px;">- "'.$fileName.'" : '.$extMessage.'</i><br>';
        }
        // ***** /extension *****

        // ***** vérifications finales *****
        if ($error === '') {
            if ((int) Input::file($input)['error'] !== 0) {
                switch (Input::file($input)['error']) {
                    case UPLOAD_ERR_NO_FILE:
                        $extMessage = $this->pushErrorWithFile('upload_err_nofile');
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $extMessage = $this->pushErrorWithFile('upload_err_size');
                        break;
                }
            }
        } else {
            $result = $this->pushErrorWithFile('error').' :<br>'. $error;
            $this->validator->addErrorWithInput($input, rtrim($result, '<br>'));
        }
        // ***** /vérifications finales *****
    }

    /**
     * Upload multiple.
     */
    public function verifyMultipleFile(string $input, array $value): void
    {
        $error = '';

        $fileName = Input::file($input)['name']; // nom du fichier
        $fileSize = Input::file($input)['size']; // poid du fichier

        // ***** nom - format *****
        if (isset($value['format_file'])) {
            foreach ($fileName as $oneFileName) {
                if (preg_match(Validator::REGEX_CHARACTERS_PROHIBITED_NAME_FILE, $oneFileName)) {
                    $error .= '<i style="font-size:16px;">- "'.$oneFileName.'" : '.$this->pushErrorWithFile('format_name').'</i><br>';
                }
            }
        }
        // ***** /nom - format *****

        // ***** nom pas déjà pris ***** 
        if (isset($value['name_not_taken'])) {
            $listRep = scandir($value['path']);
            foreach ($fileName as $oneFileName) {
                if (in_array($oneFileName, $listRep)) {
                    $error .= '<i style="font-size:16px;">- "'.$oneFileName.'" : '.$this->pushErrorWithFile('name_not_taken').'</i><br>';
                }
            }
        }
        // ***** /nom pas déjà pris *****

        // ***** nom - length *****       
        if (isset($value['max_length_name'])) {
            foreach ($fileName as $oneFileName) {
                if (mb_strlen($oneFileName) > $value['max_length_name']) {
                    $error .= '<i style="font-size:16px;">- "'.$oneFileName.'" : '.$this->pushErrorWithFile('max_length_name', $value).'</i><br>';
                }
            }
        }
        // ***** /nom - length *****

        // ***** poid *****
        if (isset($value['max_size'])) {
            foreach ($fileName as $oneFileName) {

            }
            
            foreach ($fileSize as $oneFileSize) {
                if ($oneFileSize > $value['max_size']) {
                    $error .= '<i style="font-size:16px;">- "'.$oneFileName.'" : '.$this->pushErrorWithFile('max_size', $value).'</i><br>';
                }
            }
        }
        // ***** /poid *****

        // ***** extension *****
        if ($value['extension'] === 'image') {
            $extension = Security::extsImgValid();
            $extMessage = $this->pushErrorWithFile('extension_image');
        } elseif ($value['extension'] === 'file') {
            $extension = Security::extsFileValid();
            $extMessage = $this->pushErrorWithFile('extension_file');
        }

        foreach ($fileName as $oneFileName) {
            if (!in_array( Security::getExtFile(strtolower($oneFileName)), $extension )) {
                $error .= '<i style="font-size:16px;">- "'.$oneFileName.'" : '.$extMessage.'</i><br>';
            }
        }
        // ***** /extension *****

        // ***** vérifications finales *****
        if ($error === '') {
            foreach (Input::file($input)['error'] as $oneErrorFile) {
                if ((int) $oneErrorFile !== 0) {
                    switch ($oneErrorFile) {
                        case UPLOAD_ERR_NO_FILE:
                            $extMessage = $this->pushErrorWithFile('upload_err_nofile');
                            break;
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $extMessage = $this->pushErrorWithFile('upload_err_size');
                            break;
                    }
                }
            }
        } else {
            $result = $this->pushErrorWithFile('error').' :<br>'. $error.Helper::lang('validation')['file']['upload_canceled'];
            $this->validator->addErrorWithInput($input, rtrim($result, '<br>'));
        }
        // ***** /vérifications finales *****
    }

    /**
     * Si il y a une erreur.
     *
     * @param string $key - Key dans tableaux inclut dans resources/lang...
     * @param null|string $value - Pour éventuellemnt {value} dans tableaux inclut dans resources/lang...
     */
    public function pushErrorWithFile(string $key, $value=null): string
    {
        $errorMessage = str_replace('{field}', $this->validator->getLabel(), Helper::lang('validation')['file'][$key]);

        if ($value && isset($value[$key])) {
            $errorMessage = str_replace('{value}', $value[$key], $errorMessage);
        }

        return $errorMessage;
    }
}
