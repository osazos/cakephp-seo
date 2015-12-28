<?php
/**
 * @todo base CakePHP template
 */
?>
    <?php echo $this->Form->create($seoUri, ['url' => ['plugin' => 'Seo', 'prefix' => 'admin', 'controller' => 'SeoUris', 'action' => 'edit', $seoUri->id]]); ?>
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-icon">
                        <i class="fa fa-smile-o"></i>
                    </span>
                    <span class="panel-title">SEO</span>
                </div>
                <div class="panel-body">
                    <?php
                        echo $this->Form->input('uri', ['disabled' => true]);
                    ?>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title">Balise Canonical</span>
                </div>
                <div class="panel-body pl5 pr5">
                    <table class="table">
                        <tr>
                            <td><?php echo $this->Form->input('seo_canonical.canonical', ['label' => false, 'placeholder' => 'Url canonique']); ?></td>
                            <td><?php echo $this->Form->input('seo_canonical.active', []); ?>  </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title">Balise Title</span>
                </div>
                <div class="panel-body">
                    <?php echo $this->Form->input('seo_title.title', ['label' => false]); ?>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title">Meta Tags</span>
                </div>
                <div class="panel-body pl5 pr5">
                    <table class="table">

                        <?php foreach ($seoUri->seo_meta_tags as $key => $metaTag): ?>
                            <tr>
                                <td>
                                    <?php echo $this->Form->input('seo_meta_tags.' . $key . '.id', ['value' => $metaTag->id]); ?>
                                    <?php echo $this->Form->input('seo_meta_tags.' . $key . '.name', ['label' => false, 'placeholder' => $metaTag->name, 'value' => $metaTag->name, 'disabled' => true]); ?>    
                                </td>
                                <td><?php echo $this->Form->input('seo_meta_tags.' . $key . '.content', ['label' => false, 'value' => $metaTag->content]); ?></td>
                                <td><?php echo $this->Form->input('seo_meta_tags.' . $key . '.is_http_equiv', ['checked' => $metaTag->is_http_equiv]); ?></td>
                                <td><?php echo $this->Form->input('seo_meta_tags.' . $key . '.is_property', ['checked' => $metaTag->is_property]); ?></td>
                            </tr>
                        <?php endforeach; ?>    
                    </table>
                </div>
            </div>
            <?php echo $this->Form->submit(); ?>
    <?php echo $this->Form->end(); ?>