<?php

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var string @persistent */
    public $ajax = 'on';

    /** @var string @persistent */
    public $filterRenderType = Grido\Components\Filters\Filter::RENDER_INNER;

    public function handleCloseTip()
    {
        $this->context->httpResponse->setCookie('grido-sandbox-first', 0, 0);
        $this->redirect('this');
    }

    /**
     * Handler for operations.
     * @param string $operation
     * @param array $id
     */
    public function gridOperationsHandler($operation, $id)
    {
        if ($id) {
            $row = implode(', ', $id);
            $this->flashMessage("Process operation '$operation' for row with id: $row...", 'info');
        } else {
            $this->flashMessage('No rows selected.', 'error');
        }

        $this->redirect($operation, array('id' => $id));
    }

    /**
     * Custom condition callback for filter birthday.
     * @param string $value
     * @return array|NULL
     */
    public function gridBirthdayFilterCondition($value)
    {
        $date = explode('.', $value);
        foreach ($date as &$val) {
            $val = (int) $val;
        }

        return count($date) == 3
            ? array('birthday', '= ?', "{$date[2]}-{$date[1]}-{$date[0]}")
            : NULL;
    }

    public function actionEdit($id)
    {
        $this->flashMessage("Action '$this->action' for row with id: $id done.", 'success');
        $this->redirect('default');
    }

    public function actionDelete()
    {
        $id = $this->getParameter('id');
        $id = is_array($id) ? implode(', ', $id) : $id;
        $this->flashMessage("Action '$this->action' for row with id: $id done.", 'success');
        $this->redirect('default');
    }

    public function actionPrint()
    {
        $id = $this->getParameter('id');
        $id = is_array($id) ? implode(', ', $id) : $id;
        $this->flashMessage("Action '$this->action' for row with id: $id done.", 'success');
        $this->redirect('default');
    }

    public function renderDefault()
    {
        $this['grid']; //WORKAROUND! A better visualization of the error 500..
    }

    /**********************************************************************************************/

    public function templatePrepareFilters($template)
    {
        $latte = new Nette\Latte\Engine;

        $set = new Nette\Latte\Macros\MacroSet($latte->getCompiler());
        $set->addMacro('scache', '?>?<?php echo strtotime(date(\'Y-m-d hh \')); ?>"<?php');

        $template->registerFilter($latte);
    }

    public function beforeRender()
    {
        $baseUri = $this->context->parameters['baseUri'];
        $this->template->baseUri = $baseUri ? $baseUri : $this->template->basePath;
        $this->template->first = $this->context->httpRequest->getCookie('grido-sandbox-first', 1);
    }
}
