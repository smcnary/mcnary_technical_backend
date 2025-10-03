import React, { useState, useEffect } from 'react';
import { Button } from './button';
import { Badge } from './badge';
import { Avatar, AvatarFallback, AvatarImage } from './avatar';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from './dropdown-menu';
import {
  Bell,
  User,
  Settings,
  LogOut,
  Check,
  X,
  MoreHorizontal
} from 'lucide-react';
import { useAuth } from '../../hooks/useAuth';
import { useData } from '../../hooks/useData';

interface Notification {
  id: string;
  title: string;
  message?: string;
  type: 'info' | 'success' | 'warning' | 'error';
  isRead: boolean;
  actionUrl?: string;
  actionLabel?: string;
  createdAt: string;
}

interface UserAvatarProps {
  showNotifications?: boolean;
  className?: string;
}

export default function UserAvatar({ showNotifications = true, className = '' }: UserAvatarProps) {
  const { user, logout } = useAuth();
  const { notifications, getNotifications, markNotificationAsRead, markAllNotificationsAsRead } = useData();
  const [isOpen, setIsOpen] = useState(false);
  const [notificationsOpen, setNotificationsOpen] = useState(false);
  const [isClient, setIsClient] = useState(false);

  // Prevent hydration mismatch
  useEffect(() => {
    setIsClient(true);
  }, []);

  // Load notifications when component mounts
  useEffect(() => {
    if (showNotifications && isClient) {
      getNotifications();
    }
  }, [showNotifications, getNotifications, isClient]);

  const unreadCount = notifications.filter(n => !n.isRead).length;
  const recentNotifications = notifications.slice(0, 5);

  const handleLogout = async () => {
    try {
      await logout();
      window.location.href = '/login';
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  const handleNotificationClick = async (notification: Notification) => {
    if (!notification.isRead) {
      await markNotificationAsRead(notification.id);
    }
    
    if (notification.actionUrl) {
      window.location.href = notification.actionUrl;
    }
  };

  const handleMarkAllRead = async () => {
    await markAllNotificationsAsRead();
  };

  const getNotificationIcon = (type: string) => {
    switch (type) {
      case 'success':
        return <Check className="h-4 w-4 text-green-500" />;
      case 'warning':
        return <X className="h-4 w-4 text-yellow-500" />;
      case 'error':
        return <X className="h-4 w-4 text-red-500" />;
      default:
        return <Bell className="h-4 w-4 text-blue-500" />;
    }
  };

  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(n => n[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);
  };

  if (!user || !isClient) {
    return (
      <div className={`flex items-center gap-3 ${className}`}>
        {showNotifications && (
          <div className="w-8 h-8 bg-gray-200 rounded-full animate-pulse"></div>
        )}
        <div className="w-8 h-8 bg-gray-200 rounded-full animate-pulse"></div>
      </div>
    );
  }

  return (
    <div className={`flex items-center gap-3 ${className}`}>
      {/* Notifications */}
      {showNotifications && (
        <div className="relative">
          <Button
            variant="ghost"
            size="sm"
            onClick={() => setNotificationsOpen(!notificationsOpen)}
            className="relative"
          >
            <Bell className="h-5 w-5" />
            {unreadCount > 0 && (
              <Badge 
                variant="destructive" 
                className="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center p-0 text-xs"
              >
                {unreadCount > 99 ? '99+' : unreadCount}
              </Badge>
            )}
          </Button>

          {/* Notifications Dropdown */}
          {notificationsOpen && (
            <div className="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50">
              <div className="p-4 border-b border-gray-200 dark:border-gray-700">
                <div className="flex items-center justify-between">
                  <h3 className="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                  {unreadCount > 0 && (
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={handleMarkAllRead}
                      className="text-xs"
                    >
                      Mark all read
                    </Button>
                  )}
                </div>
              </div>
              
              <div className="max-h-96 overflow-y-auto">
                {recentNotifications.length === 0 ? (
                  <div className="p-4 text-center text-gray-500 dark:text-gray-400">
                    No notifications
                  </div>
                ) : (
                  recentNotifications.map((notification) => (
                    <div
                      key={notification.id}
                      className={`p-4 border-b border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 ${
                        !notification.isRead ? 'bg-blue-50 dark:bg-blue-900/20' : ''
                      }`}
                      onClick={() => handleNotificationClick(notification)}
                    >
                      <div className="flex items-start gap-3">
                        {getNotificationIcon(notification.type)}
                        <div className="flex-1 min-w-0">
                          <p className={`text-sm font-medium ${
                            !notification.isRead ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300'
                          }`}>
                            {notification.title}
                          </p>
                          {notification.message && (
                            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                              {notification.message}
                            </p>
                          )}
                          <p className="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {new Date(notification.createdAt).toLocaleDateString()}
                          </p>
                        </div>
                        {!notification.isRead && (
                          <div className="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1" />
                        )}
                      </div>
                    </div>
                  ))
                )}
              </div>
              
              {notifications.length > 5 && (
                <div className="p-3 border-t border-gray-200 dark:border-gray-700">
                  <Button
                    variant="ghost"
                    size="sm"
                    className="w-full text-xs"
                    onClick={() => {
                      // Navigate to full notifications page
                      window.location.href = '/notifications';
                    }}
                  >
                    View all notifications
                  </Button>
                </div>
              )}
            </div>
          )}
        </div>
      )}

      {/* User Avatar */}
      <DropdownMenu open={isOpen} onOpenChange={setIsOpen}>
        <DropdownMenuTrigger asChild>
          <Button variant="ghost" className="relative h-8 w-8 rounded-full">
            <Avatar className="h-8 w-8">
              <AvatarImage src={user.avatar} alt={user.name} />
              <AvatarFallback className="bg-blue-500 text-white text-sm">
                {getInitials(user.name || user.email)}
              </AvatarFallback>
            </Avatar>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent className="w-56" align="end" forceMount>
          <DropdownMenuLabel className="font-normal">
            <div className="flex flex-col space-y-1">
              <p className="text-sm font-medium leading-none">{user.name}</p>
              <p className="text-xs leading-none text-muted-foreground">
                {user.email}
              </p>
            </div>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuItem>
            <User className="mr-2 h-4 w-4" />
            <span>Profile</span>
          </DropdownMenuItem>
          <DropdownMenuItem>
            <Settings className="mr-2 h-4 w-4" />
            <span>Settings</span>
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem onClick={handleLogout}>
            <LogOut className="mr-2 h-4 w-4" />
            <span>Log out</span>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
}
