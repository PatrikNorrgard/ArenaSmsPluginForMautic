<?php
/**
 * Created by PhpStorm.
 * User: Y520
 * Date: 15.05.2018
 * Time: 17:57
 */

namespace MauticPlugin\MauticSmsGatewayBundle;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\PluginBundle\Entity\Plugin;

class MauticSmsGatewayBundle extends PluginBundleBase
{
    public static function onPluginInstall(Plugin $plugin, MauticFactory $factory, $metadata = null, $installedSchema = null)
    {
        if ($metadata === null) {
            $metadata = self::getMetadata($factory->getEntityManager());
        }

        if ($installedSchema === null) {
            parent::installPluginSchema($metadata, $factory);
        }

        if ($metadata !== null && $installedSchema !== null) {
            parent::onPluginInstall($plugin, $factory, $metadata, $installedSchema);
        }
    }

    public static function onPluginUpdate(Plugin $plugin, MauticFactory $factory, $metadata = null, Schema $installedSchema = null)
    {
        if ($metadata === null) {
            $metadata = self::getMetadata($factory->getEntityManager());
        }

        if ($installedSchema === null) {
            $installedSchema = new Schema();
        }

        if ($metadata !== null && $installedSchema !== null) {
            parent::updatePluginSchema($metadata, $installedSchema, $factory);
        }
    }

    /**
     * Fix: plugin installer doesn't find metadata entities for the plugin
     * PluginBundle/Controller/PluginController:410.
     *
     * @param EntityManager $em
     *
     * @return array|null
     */
    private static function getMetadata(EntityManager $em)
    {
        $allMetadata   = $em->getMetadataFactory()->getAllMetadata();
        $currentSchema = $em->getConnection()->getSchemaManager()->createSchema();

        $classes = [];

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach ($allMetadata as $meta) {
            if (strpos($meta->namespace, 'MauticPlugin\\MauticSmsGatewayBundle') === false) {
                continue;
            }

            $table = $meta->getTableName();

            if ($currentSchema->hasTable($table)) {
                continue;
            }

            $classes[] = $meta;
        }

        return $classes ?: null;
    }
}