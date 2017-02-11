<script>
    $(document).ready(function(){
        $('.sidebar-dropdown').dropdown_v2({
            contentsClass : 'sub-items',
            closeOnOutsideClick : false,
            closeWhenOtherOpens : true
        });
    });
</script>
<div class="item-wrapper<?php if($this->active == $name){ echo " active open"; } ?><?php
    if(isset($this->sub_links[$name])){ echo ' sidebar-dropdown'; } ?>">
	<div class="item <?php if($this->active == $name){ echo "active"; } ?>">

		<?php if(isset($data['href'])){ ?>
		<a class="item-main" href="<?php echo $data['href']; ?>">
	    <?php }else{ ?>
        <div class="item-main">
        <?php } ?>
			<span class="item-title">
				<span class="text-left"><?php echo $this->text_left; ?></span><!--
				--><?php if(isset($data['custom_data']['content-left'])){ ?>
		              <span class="content-left"><?php echo $data['custom_data']['content-left']; ?></span>
		        <?php }
					echo $this->text_inner_left; ?><?php echo $name; ?>
					<?php if(isset($data['custom_data']['content-right'])) echo $data['custom_data']['content-right']; ?>
			</span>
        <?php if(isset($data['subtitle']) && $data['subtitle'] !== ''){ ?>
              <div class="note"><?php echo $data['subtitle']; ?></div>
        <?php } ?>
        <?php if(isset($data['href'])){ ?>
        </a>
        <?php }else{ ?>
        </div>
        <?php } ?>
        <?php if(isset($this->sub_links[$name])){ ?>
            <div class="toggle <?php if($this->active == $name){ echo "active"; } ?>">
                <i class="opened">-</i>
                <i class="closed">+</i>
            </div>
        <?php } ?>
	</div>

	<?php if(isset($this->sub_links[$name])){ ?>
		<div class="sub-items">
		<?php foreach($this->sub_links[$name] as $key => $value){ ?>
		<div class="item <?php if($this->sub_active == $key){ echo "active"; } ?>">
			<a href="<?php echo $value['href']; ?>">
				<span class="item-title"><?php echo $key;?></span>
			</a>
		</div>
		<?php } ?>
		</div>
	<?php } ?>
</div>