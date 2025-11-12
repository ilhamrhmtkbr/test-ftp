<?php

namespace ilhamrhmtkbr\App\Facades;

use ilhamrhmtkbr\App\Helper\StringHelper;

class Validation
{
    private array $errors = [];

    public function make(array $request): array
    {
        foreach ($request as $inputName => $data) {
            $parameter = [$inputName, $data[0]]; // $data[0] = values dari input $request->field; yang diambil dari $_POST/$_FILE
            if (strpos($data[1], '|')) {
                $terms = explode('|', $data[1]); // $terms / $data[1] = 'required|mustString'
                foreach ($terms as $function) { // pecah
                    $functionName = $function; // nama $function
                    if (strpos($function, ':')) { // kalo ada 'must:1,2' 
                        $fragment = explode(':', $function); // pecah jadi '['must', '1,2']'
                        $functionName = $fragment[0]; // nama $function di ambil dari $fragment element pertama
                        $condition = $fragment[1]; // set $condition awal di ambil dari $fragment element kedua misal cuma
                        if ($functionName != 'mustPasswordConfirm') {
                            if (strpos($fragment[1], '.')) { // kalo enum:2.3.4.5
                                $condition = explode('.', $fragment[1]);
                                array_push($parameter, $condition);
                            } elseif (strpos($fragment[1], ',')) { // kalo must:2,3
                                $condition = explode(',', $fragment[1]);
                                array_push($parameter, $condition);
                            }
                        } else {
                            array_push($parameter, $condition);
                        }
                    }
                    if (method_exists($this, $functionName)) {
                        call_user_func_array([$this, $functionName], $parameter);
                    } else {
                        throw new \Exception("Metode '{$functionName}' tidak ditemukan.");
                    }
                }
            } else {
                $functionName = $data[1];
                if (method_exists($this, $functionName)) {
                    call_user_func_array([$this, $functionName], $parameter);
                } else {
                    throw new \Exception("Metode '{$functionName}' tidak ditemukan.");
                }
            }
        }
        return $this->errors;
    }

    private function validValue(string $inputName, $value): bool
    {
        if (!$value) {
            $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " tidak boleh kosong.";
            return false;
        }

        return true;
    }

    private function required(string $inputName, $value): void
    {
        if ($this->validValue($inputName, $value)) {
            $sanitizedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            if (empty($sanitizedValue)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " tidak boleh kosong.";
            }
        }
    }

    private function mustString(string $inputName, $value, array $parameter = []): void
    {
        if ($this->validValue($inputName, $value)) {
            $min = $parameter[0] ?? 3;
            $max = $parameter[1] ?? 255;
            if (is_numeric($value)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus berupa string.";
            }

            $length = strlen($value);
            if ($length < $min || $length > $max) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus memiliki panjang antara $min dan $max karakter.";
            }
        }
    }

    private function mustNumeric(string $inputName, $value, $min = 1): void
    {
        if ($this->validValue($inputName, $value)) {
            if (!is_numeric($value)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus berupa string.";
            }

            $length = strlen($value);
            if ($length < $min) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus memiliki panjang minimal $min karakter.";
            }
        }
    }

    private function mustEnum(string $inputName, $value, array $data): void
    {
        if ($this->validValue($inputName, $value)) {
            if (!in_array($value, $data)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus berupa pilihan yang disediakan.";
            }
        }
    }

    private function isValidFileImage(string $inputName, $image, string|int|null $maxSize = 1048576, $allowedTypes = ['image/jpeg', 'image/png']): void
    {
        if ($image['error'] == UPLOAD_ERR_NO_FILE) {
            $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " tidak valid atau tidak diunggah dengan benar.";
        }

        if ($image['size'] > $maxSize) {
            $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " melebihi ukuran maksimal {$maxSize} byte.";
        }

        if (!in_array($image['type'], $allowedTypes)) {
            $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " bukan tipe file yang diizinkan.";
        }
    }

    private function mustBeEmail(string $inputName, $value): void
    {
        if ($this->validValue($inputName, $value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus berupa alamat email yang valid.";
            }
        }
    }


    private function mustPasswordConfirm(string $inputName, $value, $value2): void
    {
        if ($this->validValue($inputName, $value)) {
            if ($value != $value2) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " tidak sama dengan " . $value2 . '.';
            }
        }
    }

    private function mustPasswordCombination(string $inputName, $value): void
    {
        if ($this->validValue($inputName, $value)) {
            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/';

            if (!preg_match($pattern, $value)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus mengandung huruf besar, huruf kecil, angka, dan simbol.";
            }
        }
    }

    private function mustBeSocialMediaLink(string $inputName, $value): void
    {
        if ($this->validValue($inputName, $value)) {
            $socialMediaPattern = '/^(https?:\/\/)?(www\.)?(facebook|twitter|instagram|linkedin|tiktok)\.com\/[a-zA-Z0-9_]{3,30}$/';

            if (!preg_match($socialMediaPattern, $value)) {
                $this->errors[$inputName][] = StringHelper::toCapitalize($inputName) . " harus berupa link media sosial yang valid (misalnya: Facebook, Instagram, Twitter, dll).";
            }
        }
    }
}
