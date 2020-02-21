# <?php
if ($entity instanceOf \phpDocumentor\Reflection\Php\Interface_) {
    e('`interface` ');
} else {
    if ($entity->isAbstract()) {
        e('`abstract` ');
    }

    if ($entity->isFinal()) {
        e('`final` ');
    }
}
?> <?php e($entity->getName()); ?>


Fully Qualified: [`<?php e($entity->getFqsen()); ?>`](<?php e($relativeSourceLocation); ?>)


<?php $entity->getDocBlock() ? w($entity->getDocBlock()->getDescription()) : ''; ?>

<?php if (count($properties)): ?>
Property|Type|Default|Description
--------|----|-------|-----------
<?php foreach ($properties as $property): ?>
<?php e($property->isStatic() ? '`static` ' : '')?>`<?php e($property->getName()); ?>`|`<?php e(linkOwn($targetFile, implode('`, `', $property->getTypes()))); ?>`|`<?php e($property->getDefault() ?: ''); ?>`|<?php e($property->getDocBlock() ? $property->getDocBlock()->getSummary() : ''); ?>

<?php endforeach; ?>
<?php endif; ?>

<?php if (count($methods)): ?>
## Methods

<?php foreach ($methods as $method): ?>
* [<?php e($method->name) ?>()](#<?php makeAnchor($method->name) ?>)
<?php endforeach; ?>


<?php foreach ($methods as $method): ?>
### <?php e($method->name) ?>()


```php
<?php e(linkOwn($targetFile, $method->signature)); ?>

```


<?php $method->summary ? e('*' . $method->summary . '*') : ''; ?>


<?php $method->description ? e($method->description) : ''; ?>


<?php if (count($method->arguments)): ?>
Argument|Type|Default|Description
--------|----|-------|-----------
<?php foreach ($method->arguments as $argument): ?>
`<?php e('$' . $argument->name); ?>`|<?php e(($argument->isByReference ? '`&` ' : '') . ($argument->isVariadic ? '`…` ' : '') . linkOwn($targetFile, '`' . $argument->type . '`')); ?>|`<?php e($argument->default ?: ''); ?>`|<?php e($argument->description); ?>

<?php endforeach; ?>
<?php endif; ?>

Return Value: <?php e(linkOwn($targetFile, '`' . $method->return . '`')); ?>


<?php endforeach; ?>
<?php endif; ?>
