<?php $view->extend('MauticCoreBundle:Default:content.html.php'); ?>
<?php $view['slots']->set('headerTitle', $view['translator']->trans('plugin.smsgateway.show.provider_show.title') . ucfirst($entity->getProvider())); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <form>
                <fieldset disabled>
                    <div class="form-group">
                        <label for="disabledTextInput">Username</label>
                        <input type="text" class="form-control" placeholder="<?php echo $entity->getUsername(); ?>">
                    </div>
                </fieldset>
                <fieldset disabled>
                    <div class="form-group">
                        <label for="disabledTextInput">Client ID</label>
                        <input type="text" class="form-control" placeholder="<?php echo $entity->getClientId(); ?>">
                    </div>
                </fieldset>
                <fieldset disabled>
                    <div class="form-group">
                        <label for="disabledTextInput">Callback URL</label>
                        <input type="text" class="form-control" placeholder="<?php echo $entity->getCallbackUrl(); ?>">
                    </div>
                </fieldset>
                <fieldset disabled>
                    <div class="form-group">
                        <label for="disabledTextInput">Sender</label>
                        <input type="text" class="form-control" placeholder="<?php echo $entity->getSender(); ?>">
                    </div>
                </fieldset>
                <fieldset disabled>
                    <div class="form-group">
                        <label for="disabledTextInput">Provider</label>
                        <input type="text" class="form-control" placeholder="<?php echo ucfirst($entity->getProvider()); ?>">
                    </div>
                </fieldset>
            </form>
            <a href="<?php echo $view['router']->generate('plugin_smsgateway_settings_provider_edit', ['providerId' => $entity->getId()]); ?>" class="btn btn-primary"><i class="fa fa-cog"></i> <?php echo $view['translator']->trans('plugin.smsgateway.show.provider_show.btn_edit'); ?></a>
            <form action="<?php echo $view['router']->generate('plugin_smsgateway_settings_provider_delete', ['providerId' => $entity->getId()]); ?>" method="POST" style="display: inline-block">
                <button type="submit" class="btn btn-danger"><i class="fa fa-cog"></i> <?php echo $view['translator']->trans('plugin.smsgateway.show.provider_show.btn_delete'); ?></button>
            </form>
        </div>
    </div>
</div>
