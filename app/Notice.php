<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model {

    /**
     * Fillable fields for a new notice.
     * @var array
     */
    protected $fillable = [
        'infringing_title',
        'infringing_link',
        'original_link',
        'original_description',
        'template',
        'content_removed',
        'provider_id'
    ];

    /**
     * Instantiate new Notice instance with $attributes.
     *
     * @param array $attributes
     * @return static
     */
    public static function open(array $attributes)
    {
        return new static($attributes);
    }


    /**
     * set the email template for the notice
     *
     * @param string $template
     */
    public function userTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * A notice belongs to a recipient/provider.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo('App\Provider', 'provider_id');
    }

    /**
     * A notice is created by a user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    /**
     * GEt the email address for the recipient of the DMCA.
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->recipient->copyright_email;
    }

    /**
     * Get the email address of the user who created the notice.
     * @return string
     */
    public function getOwnerEmail()
    {
        return $this->user->email;
    }

}
