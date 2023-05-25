<?php
class ComposerShortCode_vc_posts_slider extends ComposerShortCode {

    protected function getPostThumbnail($post_id, $grid_thumb_size = 'full') {

        $nthumbs = Composer::getSmartBlogPostsThumbSizes();

        if (in_array($grid_thumb_size, array_values($nthumbs))) {
            return "{$post_id}-{$grid_thumb_size}.jpg";
        } else {
            return "{$post_id}.jpg";
        }

    }

}
