<?php

//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;

class FormController
{
    /**
     * Registration page with form
     */
    public function index()
    {
        $view = new View();
        $view->render('form', []);
    }

    /**
     * Form submit
     */
    public function submit()
    {
        $user = "Tena";

        //get $_POST data, validate data
        $data = $this->_validate($_POST);

        if($data === false) {
            header('Location: ' . App::config('url').'form/index');
        }

        // write to database
        $connection = App::connect();

        $sql = 'INSERT INTO `time_tracker`
                (`id`,`user`, `amount`, `description`, `work_type`, `date`)
                VALUES (?, ?, ?, ?, ?, ?)';

        $stmt = $connection->prepare($sql);
        //array_unshift($data, $user);
        //$stmt->execute($data);
        $stmt->execute(array( NULL, $user, $data['amount'], $data['description'], $data['work_type'], $data['date']));


//        $sql = 'SELECT * FROM `time_tracker` ORDER BY `id`';
//
//        $stmt = $connection->prepare($sql);
//        $stmt->execute();
//        $result = $stmt->fetchAll();
//        print_r($result);

        // redirect to thank you page
        header('Location: ' . App::config('url').'form/thankyou');
    }

    /**
     * @param $data
     * @return array|bool
     */
    private function _validate($data)
    {
        $required = ['amount', 'description', 'work_type', 'date'];

        // remove unknown keys from data if any
        $data = array_diff_key($data, $required);

        //validate required keys
        foreach($required as $key) {
            if(!isset($data[$key])) {
                return false;
            }

            // trim (strip whitespaces from values) then check if empty
            $data[$key] = trim((string)$data[$key]);
            if(empty($data[$key])) {
                return false;
            }
        }

        return $data;
    }

    /**
     * Thank you page
     */
    public function thankyou()
    {
        $view = new View();
        $view->render('thankyou');
    }

    public function listIt()
    {
        $connection = App::connect();

        $sql = 'SELECT * FROM `time_tracker`
                WHERE `user`=:user';

        $stmt = $connection->prepare($sql);
        //array_unshift($data, $user);
        //$stmt->execute($data);
        $stmt->execute(array('user' => 'Tena'));
        $data = $stmt->fetchAll();

        $view = new View();
        $view->render('list', ['data'=>$data]);
    }

    public function erase()
    {
        $connection = App::connect();

        $sql = "DELETE FROM `time_tracker` WHERE `id`=:id";
        $id = $_POST['erase'];
        $stmt = $connection->prepare($sql);
        $stmt->execute(array('id' => $id));

        //echo "Record was erased?";
        header('Location: ' . App::config('url').'form/listIt');
    }

}