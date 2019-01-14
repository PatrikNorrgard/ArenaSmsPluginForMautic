<?php $view->extend('MauticCoreBundle:Default:content.html.php'); ?>
<?php $view['slots']->set('headerTitle', $view['translator']->trans('plugin.smsgateway.statuses.index.title')); ?>

<div class="container">
    <div class="row">
        <div class="clo-sm-12">
            <table class="table table-striped">
                <thead>
                    <td>#</td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateway.statuses.index.table.phone'); ?></td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateway.statuses.index.table.status'); ?></td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateway.statuses.index.table.delivery_date'); ?></td>
                </thead>
                <tbody>
                    <?php $iterator = 1; ?>
                    <?php foreach($statuses as $status): ?>
                        <tr>
                            <td><?php echo $iterator; ?></td>
                            <td><?php echo $status->getPhone(); ?></td>
                            <td><?php echo $status->getStatus(); ?></td>
                            <td><?php echo $status->getDeliveredDate()->format('Y-m-d H:I:s'); ?></td>
                        </tr>
                        <?php $iterator++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>