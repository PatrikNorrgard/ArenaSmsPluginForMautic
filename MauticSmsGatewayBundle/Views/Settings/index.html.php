<?php $view->extend('MauticCoreBundle:Default:content.html.php'); ?>
<?php $view['slots']->set('headerTitle', $view['translator']->trans('plugin.smsgateway.settings.index.title')); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php if (!empty($entities)): ?>
                <ul class="list-group">
                    <?php foreach ($entities as $entity): ?>
                        <li class="list-group-item row">
                            <div class="col-xs-11">
                                <a href="<?php echo $view['router']->generate('plugin_smsgateway_settings_provider_show', ['providerId' => $entity->getId()]); ?>">Settings for <?php echo ucfirst($entity->getProvider()); ?></a>
                            </div>
                            <div class="col-xs-1 text-center">
                                <a href="<?php echo $view['router']->generate('plugin_smsgateway_settings_provider_edit', ['providerId' => $entity->getId()]); ?>"><i class="fa fa-cog fa-lg"></i></a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <a href="<?php echo $view['router']->generate('plugin_smsgateway_settings_provider_create')?>" class="btn btn-info"><i class="fa fa-plus"></i> <?php echo $view['translator']->trans('plugin.smsgateway.settings.index.btn_add_provider'); ?></a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-success text-center" role="alert">
                    <h4 class="alert-heading mb-20"><b><?php echo $view['translator']->trans('plugin.smsgateway.settings.index.greetings'); ?></b></h4>
                    <p><?php echo $view['translator']->trans('plugin.smsgateway.settings.index.thanks'); ?></p>
                    <p><?php echo $view['translator']->trans('plugin.smsgateway.settings.index.firsttime'); ?></p>
                    <hr>
                    <a href="<?php echo $view['router']->generate('plugin_smsgateway_settings_provider_create')?>" class="btn btn-info"><?php echo $view['translator']->trans('plugin.smsgateway.settings.index.providers_link'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>