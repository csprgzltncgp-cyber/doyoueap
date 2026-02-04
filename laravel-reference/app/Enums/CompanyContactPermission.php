<?php

namespace App\Enums;

enum CompanyContactPermission: string
{
    case TYPE_1 = 'chat-video';
    case TYPE_2 = 'phone';
    case TYPE_3 = 'personal';
    case TYPE_4 = 'chat-video-phone-personal';
    case TYPE_5 = 'chat-video-phone';
    case TYPE_6 = 'chat-video-personal';
    case TYPE_7 = 'phone-personal';
    case TYPE_8 = 'phone-email';
    case TYPE_9 = 'phone-chat-video';
}
