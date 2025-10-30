<div class="board-view-right" >
  
<?php

    echo $Ads->outAdViewPrice( ["data" => $data["ad"]] );

    echo $Ads->adAuctionSidebar( $data );
    echo $Ads->adSidebar( $data ); 

    echo $Banners->out( ["position_name"=>"ad_view_sidebar", "current_id_cat"=>$data["ad"]["category_board_id"], "categories"=>$getCategoryBoard] ); 

?>

</div>