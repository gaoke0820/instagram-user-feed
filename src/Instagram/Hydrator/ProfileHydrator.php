<?php

declare(strict_types=1);

namespace Instagram\Hydrator;

use Instagram\Utils\EndpointHelper;
use Instagram\Model\{Media, Profile};

class ProfileHydrator
{
    /**
     * @var Profile
     */
    private $profile;

    public function __construct(\StdClass $data)
    {
        $this->profile = new Profile();

        $this->profile->setId($data->id);
        $this->profile->setUserName($data->username);
        $this->profile->setFullName($data->full_name);
        $this->profile->setBiography($data->biography);
        $this->profile->setExternalUrl($data->external_url);
        $this->profile->setFollowers($data->edge_followed_by->count);
        $this->profile->setFollowing($data->edge_follow->count);
        $this->profile->setProfilePicture($data->profile_pic_url_hd);
        $this->profile->setPrivate($data->is_private);
        $this->profile->setVerified($data->is_verified);
        $this->profile->setMediaCount($data->edge_owner_to_timeline_media->count);

        foreach ($data->edge_owner_to_timeline_media->edges as $item) {
            $node = $item->node;

            $media = new Media();

            $media->setId($node->id);
            $media->setTypeName($node->__typename);

            if ($node->edge_media_to_caption->edges) {
                $media->setCaption($node->edge_media_to_caption->edges[0]->node->text);
            }

            $media->setHeight($node->dimensions->height);
            $media->setWidth($node->dimensions->width);

            $media->setThumbnailSrc($node->thumbnail_src);
            $media->setDisplaySrc($node->display_url);

            $date = new \DateTime();
            $date->setTimestamp($node->taken_at_timestamp);

            $media->setDate($date);

            $media->setComments($node->edge_media_to_comment->count);
            $media->setLikes($node->edge_media_preview_like->count);

            $media->setLink(EndpointHelper::URL_BASE . "p/{$node->shortcode}/");

            $media->setThumbnails($node->thumbnail_resources);

            if (isset($node->location)) {
                $media->setLocation($node->location);
            }

            $media->setVideo((bool)$node->is_video);

            if (property_exists($node, 'video_view_count')) {
                $media->setVideoViewCount((int)$node->video_view_count);
            }

            $this->profile->addMedia($media);
        }
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }
}
