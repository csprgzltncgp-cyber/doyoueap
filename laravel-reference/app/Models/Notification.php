<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $display_from
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Translation> $allTranslations
 * @property-read int|null $all_translations_count
 * @property-read Translation|null $translation
 * @property-read NotificationGroupTarget|null $groupTarget
 * @property-read Collection<int, NotificationSeen> $seen
 * @property-read int|null $seen_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification onlyTrashed()
 * @method static Builder|Notification query()
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereDeletedAt($value)
 * @method static Builder|Notification whereDisplayFrom($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 * @method static Builder|Notification withTrashed()
 * @method static Builder|Notification withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Notification extends Model
{
    use SoftDeletes;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_invidual_targets')->withTimestamps();
    }

    public function groupTarget(): HasOne
    {
        return $this->hasOne(NotificationGroupTarget::class);
    }

    public function seen(): HasMany
    {
        return $this->hasMany(NotificationSeen::class);
    }

    public function allTranslations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable')->select('value', 'id', 'language_id');
    }

    public function translation()
    {
        return $this->morphOne(Translation::class, 'translatable')->where('language_id', Auth::user()->language_id)->select('value');
    }

    public static function editNotification(array $request, $id): void
    {
        $notification = self::query()->findOrFail($id);
        $notification->display_from = $request['display_from'];
        $notification->save();
        // ÉRTESÍTÉSEK SZÖVEGEI
        $langs = [];
        foreach ($request['text'] as $language => $translation) {
            if ($translation) {
                $langs[] = $language;
                Translation::query()->updateOrCreate([
                    'language_id' => $language,
                    'translatable_type' => self::class,
                    'translatable_id' => $notification->id,
                ], [
                    'value' => $translation,
                ]);
            }
        }
        if ($langs !== []) {
            Translation::query()->whereNotIn('language_id', $langs)->where('translatable_id', $notification->id)->where('translatable_type', self::class)->delete();
        } else {
            Translation::query()->where('translatable_id', $notification->id)->where('translatable_type', self::class)->delete();
        }

        // kiválasztott usereknek jelenítjük meg
        if ($request['show_for'] == 'invidual_target') {
            $notification->users()->sync($request['selected_users']);
            if ($notification->groupTarget) {
                NotificationGroupTarget::query()->where('id', $notification->groupTarget->id)->delete();
            }
        } elseif ($request['show_for'] == 'target_group') {
            $notification->users()->sync([]);
            if ($notification->groupTarget) {
                $notificationGroupTarget = NotificationGroupTarget::query()->findOrFail($notification->groupTarget->id);
            } else {
                $notificationGroupTarget = new NotificationGroupTarget;
                $notificationGroupTarget->notification_id = $notification->id;
                $notificationGroupTarget->save();
            }

            /* FELHASZNÁLÓ TÍPUSOK */
            NotificationGroupTargetUserType::query()->where('group_target_id', $notificationGroupTarget->id)->delete();

            if (isset($request['selected_target_groups'])) {
                $temp = [];
                foreach ($request['selected_target_groups'] as $type) {
                    $a = [
                        'type' => $type,
                        'group_target_id' => $notificationGroupTarget->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ];
                    $temp[] = $a;
                }
                NotificationGroupTargetUserType::query()->insert($temp);
            }

            /* ORSZÁGOK */
            NotificationGroupTargetCountry::query()->where('group_target_id', $notificationGroupTarget->id)->delete();
            if (isset($request['selected_target_group_countries'])) {
                $temp = [];
                foreach ($request['selected_target_group_countries'] as $country) {
                    $a = [
                        'country_id' => $country,
                        'group_target_id' => $notificationGroupTarget->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ];
                    $temp[] = $a;
                }
                NotificationGroupTargetCountry::query()->insert($temp);
            }

            /* JOGOSULTSÁGOK */
            NotificationGroupTargetPermission::query()->where('group_target_id', $notificationGroupTarget->id)->delete();
            if (isset($request['selected_target_group_permissions'])) {
                $temp = [];
                foreach ($request['selected_target_group_permissions'] as $permission) {
                    $a = [
                        'permission_id' => $permission,
                        'group_target_id' => $notificationGroupTarget->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ];
                    $temp[] = $a;
                }
                NotificationGroupTargetPermission::query()->insert($temp);
            }
        }
    }

    public static function createNotification(array $request): void
    {
        $notification = new self;
        $notification->display_from = $request['display_from'];
        $notification->save();

        foreach ($request['text'] as $language => $translation) {
            if ($translation) {
                Translation::query()->updateOrCreate([
                    'language_id' => $language,
                    'translatable_type' => self::class,
                    'translatable_id' => $notification->id,
                ], [
                    'value' => $translation,
                ]);
            }
        }

        // kiválasztott usereknek jelenítjük meg
        if ($request['show_for'] == 'invidual_target') {
            $notification->users()->sync($request['selected_users']);
        } elseif ($request['show_for'] == 'target_group') {
            $notificationGroupTarget = new NotificationGroupTarget;
            $notificationGroupTarget->notification_id = $notification->id;
            $notificationGroupTarget->save();

            /* USER TÍPUSOK */
            if (isset($request['selected_target_groups'])) {
                $temp = [];
                foreach ($request['selected_target_groups'] as $type) {
                    $a = [
                        'type' => $type,
                        'group_target_id' => $notificationGroupTarget->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ];
                    $temp[] = $a;
                }
                NotificationGroupTargetUserType::query()->insert($temp);
            }

            /* ORSZÁGOK */
            if (isset($request['selected_target_group_countries'])) {
                $temp = [];
                foreach ($request['selected_target_group_countries'] as $country) {
                    $a = [
                        'country_id' => $country,
                        'group_target_id' => $notificationGroupTarget->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ];
                    $temp[] = $a;
                }
                NotificationGroupTargetCountry::query()->insert($temp);
            }

            /* PERMISSIONS */
            if (isset($request['selected_target_group_permissions'])) {
                $temp = [];
                foreach ($request['selected_target_group_permissions'] as $permission) {
                    $a = [
                        'permission_id' => $permission,
                        'group_target_id' => $notificationGroupTarget->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ];
                    $temp[] = $a;
                }
                NotificationGroupTargetPermission::query()->insert($temp);
            }
        }
    }
}
