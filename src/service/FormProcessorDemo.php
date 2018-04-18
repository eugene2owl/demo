<?php

declare(strict_types = 1);

namespace Demo\Service;

class FormProcessorDemo
{
    private const VALID_MESSAGE = "Format is ok.";
    private const EMPTY = "(empty)";
    private const RAW = "Raw data: ";
    private const SANITIZED = "Sanitized data: ";

    public function getFormResults(
        string $name = "name",
        string $email = "email",
        string $age = "age",
        string $id = "identifier",
        string $gender = "gender"
    ): array
    {
        $processedNameArray = $this->processName($_POST[$name]);
        $processedEmailArray = $this->processEmail($_POST[$email]);
        $processedDepartmentIdArray = $this->processDepartmentId($_POST[$id]);
        $processedAgeArray = $this->processAge($_POST[$age]);
        $processedGenderArray = $this->processGender($_POST[$gender]);
        return [
            "name"            => $processedNameArray,
            "email"           => $processedEmailArray,
            "age"             => $processedAgeArray,
            "departmentId"    => $processedDepartmentIdArray,
            "gender"          => $processedGenderArray,
        ];
    }
    private const NAME_INVALID_MESSAGE = "Name should contain only english letters.";
    private function processName(?string $name): array
    {
        $processedNameArray = [];
        if (isset($name)) {
            $processedNameArray[] = !empty(trim($name)) ? self::RAW . $name : self::RAW . self::EMPTY;
            $isValid = ctype_alpha($name);
            $processedNameArray[] = ($isValid) ? self::VALID_MESSAGE : self::NAME_INVALID_MESSAGE;
            if (!empty(trim($name))) {
                $processedNameArray[] = self::SANITIZED . filter_var($name, FILTER_SANITIZE_STRING);
            } else {
                $processedNameArray[] = self::SANITIZED . self::EMPTY;
            }
        }
        return $processedNameArray;
    }

    private const EMAIL_INVALID_MESSAGE = "Email should have postfix.";
    private function processEmail(?string $email): array
    {
        $processedEmailArray = [];
        if (isset($email)) {
            $processedEmailArray[] = !empty(trim($email)) ? self::RAW . $email : self::RAW . self::EMPTY;
            $isValid = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
            $processedEmailArray[] = ($isValid) ? self::VALID_MESSAGE : self::EMAIL_INVALID_MESSAGE;
            if (!empty(trim($email))) {
                $processedEmailArray[] = self::SANITIZED . filter_var($email, FILTER_SANITIZE_EMAIL);
            } else {
                $processedEmailArray[] = self::SANITIZED . self::EMPTY;
            }
        }
        return $processedEmailArray;
    }

    private const AGE_INVALID_MESSAGE = "Age must be a number.";
    private function processAge(?string $age): array
    {
        $processedAgeArray = [];
        if (isset($age)) {
            $processedAgeArray[] = !empty(trim($age)) ? self::RAW . $age : self::RAW . self::EMPTY;
            $isValid = ctype_digit($age);
            $processedAgeArray[] = ($isValid) ? self::VALID_MESSAGE : self::AGE_INVALID_MESSAGE;
            if (!empty(trim($age))) {
                $processedAgeArray[] =
                    self::SANITIZED .
                    filter_var($age, FILTER_SANITIZE_NUMBER_INT) . " or " .
                    htmlentities($age);
            } else {
                $processedAgeArray[] = self::SANITIZED . self::EMPTY;
            }
        }
        return $processedAgeArray;
    }

    private const DEPARTMENT_ID_INVALID_MESSAGE = "There are only A-C departments now.";
    private function processDepartmentId(?string $id): array
    {
        $processedIdArray = [];
        if (isset($id)) {
            $processedIdArray[] = !empty(trim($id)) ? self::RAW . $id : self::RAW . self::EMPTY;
            $isValid = preg_match("/[A-C]-([0-9]){3,3}/", $id);
            $processedIdArray[] = ($isValid) ? self::VALID_MESSAGE : self::DEPARTMENT_ID_INVALID_MESSAGE;
            if (!empty(trim($id))) {
                $processedIdArray[] = self::SANITIZED . filter_var($id, FILTER_SANITIZE_STRING);
            } else {
                $processedIdArray[] = self::SANITIZED . self::EMPTY;
            }
        }
        return $processedIdArray;
    }

    private const GENDER_INVALID_MESSAGE = "Do not deceive the system.";
    private function processGender(?string $gender): array
    {
        $processedGenderArray = [];
        if (isset($gender)) {
            //$gender = "enemy input";
            $processedGenderArray[] = !empty(trim($gender)) ? self::RAW . $gender : self::RAW . self::EMPTY;
            $gendersBase = ["male" => 0, "female" => 1];
            $isValid = array_key_exists($gender, $gendersBase);
            $processedGenderArray[] = ($isValid) ? self::VALID_MESSAGE : self::GENDER_INVALID_MESSAGE;
            if (!empty(trim($gender))) {
                $processedGenderArray[] = self::SANITIZED . $gender;
            } else {
                $processedGenderArray[] = self::SANITIZED . self::EMPTY;
            }
        }
        return $processedGenderArray;
    }
}
