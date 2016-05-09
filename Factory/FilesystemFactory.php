<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle;

use Symfony\Cmf\Component\Resource\RepositoryFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Cmf\Component\Resource\Repository\PhpcrOdmRepository;

class FilesystemFactory extends RepositoryFactoryInterface
{
    public function create(array $options)
    {
        if (null === $options['base_dir']) {
            throw new \InvalidArgumentException(
                'The filesystem repository type requires a "base_dir" option to be set.'
            );
        }

        return new FilesystemRepository(
            $options['base_dir'],
            $options['symlink']
        );
    }

    public function getDefaultConfig()
    {
        return [
            'base_dir' => null,
            'symlink' => null,
        ];
    }
}

