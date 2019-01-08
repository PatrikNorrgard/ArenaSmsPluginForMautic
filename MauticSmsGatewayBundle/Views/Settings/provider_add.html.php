<?php $view->extend('MauticCoreBundle:Default:content.html.php'); ?>
<?php $view['slots']->set('headerTitle', $view['translator']->trans('plugin.smsgateaway.create.provider_add.title')); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php foreach ($view['session']->getFlash('notice') as $flash_message): ?>
                <div class="alert alert-success alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong><?php echo $flash_message ?></strong>
                </div>
            <?php endforeach ?>
            <?php echo $view['form']->start($form); ?>

            <?php echo $view['form']->row($form['provider']); ?>
            <?php echo $view['form']->row($form['username']); ?>
            <?php echo $view['form']->row($form['password']); ?>
            <?php echo $view['form']->row($form['client_id']); ?>
            <?php echo $view['form']->row($form['callback_url']); ?>
            <?php echo $view['form']->row($form['sender']); ?>
            <?php echo $view['form']->row($form['default_provider']); ?>
            <?php echo $view['form']->row($form['buttons']); ?>

            <?php echo $view['form']->end($form); ?>
        </div>
    </div>
</div>
