<?php
namespace Cassoft\AdverFeedback;

class AvitoTransformation {
    
    public function __construct() {
        
    }

    public function validateAvitoErrorReport(string $statusGeneral, string $statusAvito, array $errors) {
        $result = []; 
        $description = []; 
        if(!empty($statusAvito)) {
            $result[] = "Статус: {$statusAvito}";
        }

        switch ($statusGeneral) {
            case 'success':
                $description[] = 'Опубликовано без ошибок';
                break;
            case 'error':
                foreach($errors as $message) {
                    if($message['type'] === 'error') {
                        $message['description'] = strip_tags($message['description']);
                        $description[] = $message['description']; 
                    }
                }
                break;
            case 'problem':
                foreach($errors as $message) {
                    if($message['type'] === 'error' || $message['type'] === 'alarm') {
                        if(!empty($message['description'])) {
                            $message['description'] = strip_tags($message['description']);
                            $description[] = $message['description']; 
                        }
                    }
                }
                break;
        }

        if(empty($errors)) {
            $description[] = 'Дополнительную информации нужно уточнить в личном кабинете Авито';
        }
        return array_merge($result, $description);
    }
    
    

}
