<?php namespace toolbox; ?>
<div class="nav">
    <?php foreach($this->links as $name => $data){
        $custom_data = $data['custom_data'];
?>
<div class="nav-button-container">
    <a class="nav-button <?php
    if($this->active == $name){ echo " nav-button-current"; }
    if(isset($custom_data['dropdown_contents'])){ echo " nav-button-dropdown"; }
?>" href="<?php echo $data['href']; ?>"><?php echo $name; ?>
        <?php if(isset($custom_data['dropdown_contents'])){ ?> <i class="icon-down-dir"></i><?php } ?>
    </a>
    <?php if(isset($custom_data['dropdown_contents'])){
        $custom_data['dropdown_contents']->renderViews();
    } ?>
</div>
<?php /*
<div class="item-wrapper<?php if($this->active == $name){ echo " nav-button-current"; } ?><?php
    if(isset($this->sub_links[$name])){ echo ' sidebar-dropdown'; } ?>">
    <div class="item <?php if($this->active == $name){ echo "active"; } ?>">
        <?php if(isset($this->sub_links[$name])){ ?>
            <div class="toggle <?php if($this->active == $name){ echo "active"; } ?>">
                <i class="icon-down-open"></i>
                <i class="icon-right-open"></i>
            </div>
        <?php } ?>
        <?php if(isset($data['href'])){ ?>
        <a class="item-main" href="<?php echo $data['href']; ?>">
        <?php }else{ ?>
        <div class="item-main">
        <?php } ?>
            <span class="title">
                <span class="text-left"><?php echo $this->text_left; ?></span><!--
                --><?php echo $this->text_inner_left; ?><?php echo $name; ?>
            </span>
            <span class="note"><?php echo $data['subtitle']; ?></span>
        <?php if(isset($data['href'])){ ?>
        </a>
        <?php }else{ ?>
        </div>
        <?php } ?>
    </div>

    <?php if(isset($this->sub_links[$name])){ ?>
        <div class="sub-items">
        <?php foreach($this->sub_links[$name] as $key => $value){ ?>
        <div class="item <?php if($this->sub_active == $key){ echo "active"; } ?>">
            <a href="<?php echo $value['href']; ?>">
                <span class="title"><?php echo $key;?></span>
            </a>
        </div>
        <?php } ?>
        </div>
    <?php } ?>
</div>

<?php */
    } ?>
</div>
