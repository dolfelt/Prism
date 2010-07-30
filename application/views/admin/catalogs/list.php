<script type="text/javascript">
	$(function() {
		$(".data-table tbody tr.catalog td:not(:last-child)").click(function() {
			document.location.href = "<?php echo url::site('admin/catalogs/list'); ?>/" + $(this).parent("tr").attr('data-id');
		});
		
		$(".data-table tbody tr.product td:not(:last-child)").click(function() {
			document.location.href = "<?php echo url::site('admin/products/edit'); ?>/" + $(this).parent("tr").attr('data-path') + "/" + $(this).parent("tr").attr('data-id');
		});
		
		$(".data-table tr:not(.no-hover) td").hover(function() {
			$(this).parent("tr").find("td").addClass("hover");
		}, function () {
			$(this).parent("tr").find("td").removeClass("hover");
		})
	});
</script>

<table class="data-table">
	<thead>
		<tr>
			<th>Name</th>
			<th width="15%">Price</th>
			<th width="15%">Status</th>
			<th width="15%">Groups / Products</th>
			<th width="120">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if(isset($catalogs) && is_array($catalogs))
		{
			foreach($catalogs as $catalog)
			{
				$path_id = Hierarchy::buildid($catalog_path);
				$list_id = Hierarchy::buildid($catalog_path, $catalog['catalog_id']);
				?>
				<tr class="parent-row catalog" data-id="<?php echo $list_id; ?>">
					<td class="first">
						<?php echo $catalog['catalog_name']; ?>
					</td>
					<td><?php echo ' --- '; ?></td>
					<td><?php 
						if($catalog['catalog_enabled']==1) {
							echo 'Enabled';
						} else {
							echo 'Disabled';
						} 
					?></td>
					<td><?php echo ' --- '; ?></td>
					<td>
						<a href="<?php echo url::site('admin/catalogs/edit/'.$path_id.'/'.$catalog['catalog_id']); ?>">Edit</a> &bull;
						<a href="<?php echo url::site('admin/catalogs/delete/'.$path_id.'/'.$catalog['catalog_id']); ?>">Delete</a>
					</td>
				</tr>
				<?php
			}
		}
		
		if(isset($products) && is_array($products))
		{
			foreach($products as $product)
			{
				$path_id = Hierarchy::buildid($catalog_path);
			?>
				<tr class="product" data-path="<?php echo $path_id; ?>" data-id="<?php echo $product['product_id']; ?>">
					<td class="first">
						<?php echo $product['product_name']; ?>
					</td>
					<td><?php echo '$'.number_format($product['product_price'],2); ?></td>
					<td><?php 
						if($product['product_enabled']==1) {
							echo 'Enabled';
						} else {
							echo 'Disabled';
						} 
					?></td>
					<td><?php echo ' --- '; ?></td>
					<td>
						<a href="<?php echo url::site('admin/products/edit/'.$path_id.'/'.$product['product_id']); ?>">Edit</a> &bull;
						<a href="#">Delete</a>
					</td>
				</tr>
			<?php
			}
		}
		?>
	</tbody>
</table>