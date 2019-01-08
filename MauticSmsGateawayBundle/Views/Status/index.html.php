<?php $view->extend('MauticCoreBundle:Default:content.html.php'); ?>
<?php $view['slots']->set('headerTitle', $view['translator']->trans('plugin.smsgateaway.statuses.index.title')); ?>

<div class="container">
    <div class="row">
        <div class="clo-sm-12">
            <?php foreach ($view['session']->getFlash('notice') as $flash_message): ?>
                <div class="alert alert-success alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong><?php echo $flash_message ?></strong>
                </div>
            <?php endforeach ?>

            <?php echo $view['form']->start($form); ?>
            <?php echo $view['form']->row($form['update']); ?>
            <table class="table table-striped">
                <thead>
                    <td>#</td>
                    <td><a href="#" id="select-all">Select all</a> | <a href="#" id="deselect">Deselect</a></td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateaway.statuses.index.table.phone'); ?></td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateaway.statuses.index.table.id'); ?></td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateaway.statuses.index.table.status'); ?></td>
                    <td><?php echo $view['translator']->trans('plugin.smsgateaway.statuses.index.table.delivery_date'); ?></td>
                </thead>
                <tbody>
                    <?php $iterator = 1; ?>
                    <?php foreach($form->children['tickets'] as $status): ?>
                        <?php $label = explode('|', $view['form']->label($status)); ?>
                        <tr>
                            <td><?php echo $iterator; ?></td>
                            <td><?php echo $view['form']->widget($status); ?></td>
                            <td><?php echo strip_tags($label[0]); ?></td>
                            <td><?php echo $label[1]; ?></td>
                            <td><?php echo $label[2]; ?></td>
                            <td><?php echo strip_tags($label[3]); ?></td>
                        </tr>
                        <?php $iterator++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php echo $view['form']->end($form); ?>
        </div>
    </div>
</div>
<script>
    jQuery(function($){
        $('#select-all').click(function(){
            $('tbody tr td input[type="checkbox"]').prop('checked', true);
            return false;
        });
        $('#deselect').click(function(){
            $('tbody tr td input[type="checkbox"]').prop('checked', false);
            return false;
        });
    });
</script>