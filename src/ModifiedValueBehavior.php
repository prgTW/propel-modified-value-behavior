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
	 * Propel >= 2.0.0-beta2 finds templates only in its own `src/<ns>/` + `templates/<ns>/` layout,
	 * which this package does not use; older versions never call this method.
	 * __DIR__ rather than $path keeps subclasses defined elsewhere on these templates.
	 */
	protected function getTemplatePath(string $path): string
	{
		return __DIR__ . '/templates/';
	}
}
