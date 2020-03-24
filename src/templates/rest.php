# <?php $this->e($entity->summary); ?>


<?php $this->w($entity->description); ?>


<?php foreach ($paths as $path): ?>
## `<?php $this->e($path->request->method) ?>` `<?php $this->e($path->request->url) ?>`

<?php $path->summary ? $this->e('*' . $path->summary . '*') : ''; ?>


<?php $path->description ? $this->e($path->description) : ''; ?>


### Request Body


```
<?php $this->e($path->request->bodyType); ?>

```


### Responses

<?php foreach($path->responses as $response): ?>
Status: <?php $this->e($response->status); ?>


<?php $this->w($path->description); ?>


```
<?php $this->e($response->bodyType); ?>

```


<?php endforeach; ?>

<?php endforeach; ?>
