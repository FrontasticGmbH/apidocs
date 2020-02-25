# <?php
if ($entity instanceOf \phpDocumentor\Reflection\Php\Interface_) {
    $this->e('`interface` ');
} else {
    if ($entity->isAbstract()) {
        $this->e('`abstract` ');
    }

    if ($entity->isFinal()) {
        $this->e('`final` ');
    }
}
?> <?php $this->e($entity->getName()); ?>


Fully Qualified: [`<?php $this->e($entity->getFqsen()); ?>`](<?php $this->e($relativeSourceLocation); ?>)


<?php $entity->getDocBlock() ? $this->w($entity->getDocBlock()->getDescription()) : ''; ?>

<?php if (count($properties)): ?>
Property|Type|Default|Description
--------|----|-------|-----------
<?php foreach ($properties as $property): ?>
<?php $this->e($property->isStatic ? '`static` ' : '')?>`<?php $this->e($property->name); ?>`|`<?php $this->e($this->linkOwn($targetFile, implode('`, `', $property->types))); ?>`|<?php $this->e($property->default ? '`' . $property->default . '`' : ''); ?>|<?php $this->e($this->removeNewLines($property->summary)); ?>

<?php endforeach; ?>
<?php endif; ?>

<?php if (count($methods)): ?>
## Methods

<?php foreach ($methods as $method): ?>
* [<?php $this->e($method->name) ?>()](#<?php $this->makeAnchor($method->name) ?>)
<?php endforeach; ?>


<?php foreach ($methods as $method): ?>
### <?php $this->e($method->name) ?>()


```php
<?php $this->e($this->linkOwn($targetFile, $method->signature)); ?>

```


<?php $method->summary ? $this->e('*' . $method->summary . '*') : ''; ?>


<?php $method->description ? $this->e($method->description) : ''; ?>


<?php if (count($method->arguments)): ?>
Argument|Type|Default|Description
--------|----|-------|-----------
<?php foreach ($method->arguments as $argument): ?>
`<?php $this->e(($argument->isByReference ? '&' : '') . ($argument->isVariadic ? '…' : '') . '$' . $argument->name); ?>`|<?php $this->e($this->linkOwn($targetFile, '`' . $argument->type . '`')); ?>|<?php $this->e($argument->default ? '`' . $argument->default . '`' : ''); ?>|<?php $this->e($this->removeNewLines($argument->description)); ?>

<?php endforeach; ?>
<?php endif; ?>

Return Value: <?php $this->e($this->linkOwn($targetFile, '`' . $method->return . '`')); ?>


<?php endforeach; ?>
<?php endif; ?>
