<?php

    class Session
    {
        //セッションスタート
        public function __construct()
        {
            session_start();
        }

        /*
         *
         * Method
         *
         * */

        //ログイン
        public function login($username, $user_id, $to_url)
        {
            //CSRFトークンが正しければ
            if ($this->validate_token()) {
                session_regenerate_id(true);
                $this->set_session('user_id', $user_id);
                $this->set_session('username', $username);
                $_SESSION['csrf_token'] = $this->generate_token();
                header('Location: ' . $to_url);
                exit;
            }
            return false;
        }

        public function get_user_id()
        {
            if ($this->is_login()) {
                return $this->get_session('user_id');
            } else
                return -1;
        }

        public function get_user_name()
        {
            if ($this->is_login()) {
                return $this->get_session('username');
            } else
                return -1;
        }

        //ログアウト
        public function logout()
        {
            $_SESSION = array();
            session_destroy();
        }

        /*
         *
         * CSRF
         *
         * */

        //CSRFtokenを作成
        public function generate_token()
        {
            return hash('sha256', session_id());
        }

        //CSRFtokenの確認
        public function validate_token()
        {
            if (isset($_SESSION['csrf_token'])) {
                return $_SESSION['csrf_token'] === $this->generate_token();
            } else {
                return false;
            }
        }


        /*
         *
         * State
         *
         * */

        //ログインしているか
        public function is_login()
        {
            return $this->validate_token() && $this->get_session('username') && $this->get_session('user_id');
        }

        //ログアウトしているか
        public function is_logout()
        {
            return !$this->is_login();
        }


        /*
         *
         * Helper
         *
         * */

        //セッション変数の設定
        private function set_session($key, $value)
        {
            $_SESSION[$key] = $value;
        }

        //セッション変数の取得
        private function get_session($key)
        {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
        }

        //セッション変数の消去
        private function delete_session($key)
        {
            if (isset($_SESSION[$key])) unset($_SESSION[$key]);
        }

    }