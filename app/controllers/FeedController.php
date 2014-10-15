<?php
class FeedController extends BaseController
{
    /**
     * generate xss/atom from spreadit
     *
     * @param string $section_title name of section
     *
     * @return Roumen\Feed
     */
    protected function generate($section_title)
    {
        $section = Section::sectionFromSections(Section::getByTitle(Section::splitByTitle($section_title)));
        $posts = Post::getHotList($section->id);
        $feed = Feed::make();
        $feed->title = $section_title;
        $feed->description = "read hot posts from $section_title"; 
        $feed->link = URL::to("/s/$section_title");
        $feed->lang = 'en';

        $created_at_counter = 0;
        foreach($posts as $post) {
            $feed->add($post->title, $post->username, URL::to($post->url), date(DATE_ATOM, $post->created_at), $post->markdown);
            
            if($post->created_at > $created_at_counter) {
                $created_at_counter = $post->created_at;
            }
        }
        $feed->pubdate = date(DATE_ATOM, $created_at_counter);

        return $feed;
    }

    protected function rss($section_title="all")
    {
        return $this->generate($section_title)->render('rss');
    }

    protected function atom($section_title="all")
    {
        return $this->generate($section_title)->render('atom');
    }
}
