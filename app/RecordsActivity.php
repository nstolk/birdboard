<?php


namespace App;


trait RecordsActivity
{
    public $oldAttributes = [];

    public static function bootRecordsActivity()
    {
        foreach (static::recordableEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($model->activityDescription($event));
            });

            if ($event === 'updated') {
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }
        }
    }

    protected function activityDescription($description)
    {
        return "{$description}_" . strtolower(class_basename($this));
    }

    /**
     * @return array
     */
    protected static function recordableEvents(): array
    {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        } else {
            return ['created', 'updated'];
        }
    }

    public function recordActivity($description)
    {
        $this->activity()->create([
            'user_id' => ($this->project ?? $this)->owner->id,
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id
        ]);
    }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    protected function activityChanges()
    {
        if ($this->wasChanged()) {
            return [
                'before' => array_except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'),
                'after' => array_except($this->getChanges(), 'updated_at')
            ];
        }
    }
}