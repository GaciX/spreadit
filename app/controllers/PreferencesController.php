<?php
class PreferencesController extends BaseController
{
    protected $user;
    protected $section;

    public function __construct(User $user, Section $section)
    {
        $this->user = $user;
        $this->section = $section;
    }


    /**
     * renders user preferences page
     *
     * @return Illuminate\View\View
     */
    public function preferences()
    {
        $section_titles = function($input) {
            if(strlen(Auth::user()->$input) == 0) {
                return "";
            }

            $input_data = Auth::user()->$input;
            $arr = explode(',', $input_data);

            return implode(',', array_unique(F::map($this->section->getById($arr), function($m) {
                return $m->title;
            })));
        };

        $sections = $this->section->get();
        $section = $this->section->sectionFromEmptySection();
        
        $frontpage_show_sections   = $section_titles('frontpage_show_sections');
        $frontpage_ignore_sections = $section_titles('frontpage_ignore_sections');

        return View::make('page.user.prefs.preferences', [
            'sections'                  => $sections,
            'section'                   => $section,
            'frontpage_show_sections'   => $frontpage_show_sections,
            'frontpage_ignore_sections' => $frontpage_ignore_sections,
        ]);
    }


    /**
     * see `preferences`
     * note: moo?
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function preferencesJson()
    {
        return Response::json("moo");
    }

    /**
     * saves user preferences
     *
     * @return Illuminate\View\View
     */
    public function savePreferences()
    {
        $section_ids = function($input) {
            $input_data = Input::get($input, '');
            
            if(strlen($input_data) == 0) {
                return "";
            }
            
            $arr = explode(',', $input_data);

            return implode(',', array_unique(F::map($this->section->getByTitle($arr), function($m) {
                return $m->id;
            })));
        };
        
        $show_nsfw = Input::get('show_nsfw', 0);
        $show_nsfl = Input::get('show_nsfl', 0);

        $frontpage_show_sections   = $section_ids('frontpage_show_sections');
        $frontpage_ignore_sections = $section_ids('frontpage_ignore_sections');

        
        if($frontpage_show_sections   == "0") $frontpage_show_sections   = "";
        if($frontpage_ignore_sections == "0") $frontpage_ignore_sections = "";

        $this->user->savePreferences(Auth::id(), [
            'show_nsfw'                 => $show_nsfw,
            'show_nsfl'                 => $show_nsfl,
            'frontpage_show_sections'   => $frontpage_show_sections,
            'frontpage_ignore_sections' => $frontpage_ignore_sections,
        ]);

        $sections = $this->section->get();
        $section = $this->section->sectionFromEmptySection();

        return View::make('page.user.prefs.savedpreferences', [
            'sections' => $sections,
            'section'  => $section,
        ]);
    }


    /**
     * renders theme chooser index page
     *
     * @return Illuminate\View\View
     */
    public function theme_index()
    {
        return View::make('page.user.prefs.theme_index', ['sections' => $this->section->get()]);
    }


    /**
     * sets theme cookie and redirects
     *
     * @return Illuminate\Http\RedirectResponse
     */
    private function theme_cookie_switch($colorscheme)
    {
        return Redirect::to(Utility::backOrUrl("/preferences/theme"))->withCookie(Cookie::forever('theme', $colorscheme));

    }


    /**
     * sets theme cookie to dark
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function theme_dark()
    {
        return $this->theme_cookie_switch('dark');
    }


    /**
     * sets theme cookie to light
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function theme_light()
    {
        return $this->theme_cookie_switch('light');
    }


    /**
     * sets theme cookie to tiles
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function theme_tiles()
    {
        return $this->theme_cookie_switch('tiles');
    }


    /**
     * renders user homepage preferences page
     *
     * @return Illuminate\View\View
     */
    public function homepage()
    {
        return View::make('page.user.prefs.homepage', [
            'sections' => $this->section->get(),
            'markdown' => Auth::user()->profile_markdown,
            'css'      => Auth::user()->profile_css,
        ]);
    }


    /**
     * saves user homepage and shows saved homepage page
     *
     * @return Illuminate\View\View
     */
    public function saveHomepage()
    {   
        $profile_markdown = Input::get('data', '');
        $profile_data     = Markdown::defaultTransform(e($profile_markdown));
        $profile_css      = Input::get('css', '');

        $this->user->saveHomepage(Auth::id(), [
            'profile_data'       => $profile_data,
            'profile_markdown'   => $profile_markdown,
            'profile_css'        => $profile_css,
        ]);

        $sections = $this->section->get();
        $section = $this->section->sectionFromEmptySection();

        return View::make('page.user.prefs.savedhomepage', [
            'sections' => $sections,
            'section'  => $section,
        ]);
    }
}
