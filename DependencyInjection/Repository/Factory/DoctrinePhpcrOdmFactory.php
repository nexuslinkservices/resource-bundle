<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Cmf\Component\Resource\Repository\PhpcrOdmRepository;

class DoctrinePhpcrOdmFactory implements RepositoryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $options)
    {
        return new Definition(PhpcrOdmRepository::class, [
            new Reference('doctrine_phpcr'),
            $options['basepath'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine_phpcr_odm';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(OptionsResolver $options)
    {
        $options->setDefault('basepath', null);
    }
}
