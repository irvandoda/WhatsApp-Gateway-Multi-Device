<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'message',
        'status',
        'category',
        'description',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'message' => 'array',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the template
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for templates by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for templates by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }


    /**
     * Increment usage count and update last used timestamp
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get formatted message for campaign
     */
    public function getFormattedMessage($contact = null)
    {
        $message = $this->message;
        
        if ($contact && isset($message['message'])) {
            $message['message'] = str_replace('{name}', $contact->name ?? '', $message['message']);
        }
        
        return $message;
    }

    /**
     * Get available template types
     */
    public static function getAvailableTypes()
    {
        return [
            'text' => __('Text Message'),
            'button' => __('Button Message'),
            'list' => __('List Message'),
            'media' => __('Media Message'),
			'location' => __('Location Message'),
            'vcard' => __('VCard Message'),
            'sticker' => __('Sticker Message'),
            'product' => __('Product Message')
        ];
    }

    /**
     * Get available categories
     */
    public static function getAvailableCategories()
    {
        return [
            'marketing' => __('Marketing'),
            'notification' => __('Notification'),
            'welcome' => __('Welcome'),
            'follow_up' => __('Follow Up'),
            'reminder' => __('Reminder'),
            'promotional' => __('Promotional'),
            'support' => __('Support'),
            'other' => __('Other')
        ];
    }
}
