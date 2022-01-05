<?php
namespace Ml\Settings;

use Bitrix\Main\Application;



abstract class Main {

    protected array $request;

/*    public function __construct()
    {
        $this->request = Application::getInstance()->getContext()->getRequest()->getPostList()->toArray();
    }*/

    public function getRequest () :array{

        $this->request = Application::getInstance()->getContext()->getRequest()->getPostList()->toArray();

        return $this->request;
    }
}

