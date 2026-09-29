<?php

namespace Radekb\ModifiedValueBehavior;

use Propel\Generator\Model\Behavior;

class ModifiedValueBehavior extends Behavior
{
	/** @var  ModifiedValueModelBuilderModifier */
	private $modelBuilderModifier;

	/**
	 * @return ModifiedValueModelBuilderModifier
	 */
	public function getObjectBuilderModifier()
	{
		if (null === $this->modelBuilderModifier)
		{
			$this->modelBuilderModifier = new ModifiedValueModelBuilderModifier($this);
		}

		return $this->modelBuilderModifier;
	}

	/**
	 * Propel >= 2.0.0-beta2 resolves templates from a `src/` root it cannot find in vendor/;
	 * older versions never call this method.
	 */
	protected function getTemplatePath(string $path): string
	{
		return __DIR__ . '/templates/';
	}
}
