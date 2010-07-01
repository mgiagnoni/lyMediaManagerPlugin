<?php
  $base = '@ly_media_asset_icons?' .($popup ? 'popup=1&' : '') . 'page=';
?>
<div class="pagination">
  <?php echo link_to('First', $base . '1', array('class' => 'first'));?>
  <?php echo link_to('Prev', $base . $pager->getPreviousPage(), array('class' => 'prev'));?>
  <?php foreach ($pager->getLinks() as $page): ?>
    <?php if ($page == $pager->getPage()): ?>
      <?php echo $page ?>
    <?php else:
      echo link_to($page, $base . $page, array('class' => 'page'));
    endif; ?>
  <?php endforeach; ?>
  <?php echo link_to('Next', $base . $pager->getNextPage(), array('class' => 'next'));?>
  <?php echo link_to('Last', $base . $pager->getLastPage(), array('class' => 'last'));?>
</div>
