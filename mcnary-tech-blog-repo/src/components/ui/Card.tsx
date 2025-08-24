import { forwardRef } from 'react';
import { clsx } from 'clsx';

export interface CardProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'elevated' | 'outlined';
}

const Card = forwardRef<HTMLDivElement, CardProps>(
  ({ className, variant = 'default', ...props }, ref) => {
    const baseClasses = 'bg-white rounded-lg overflow-hidden';
    
    const variantClasses = {
      default: 'shadow-sm border border-gray-200',
      elevated: 'shadow-lg border-0',
      outlined: 'border-2 border-gray-200 shadow-none',
    };
    
    const classes = clsx(baseClasses, variantClasses[variant], className);
    
    return (
      <div
        className={classes}
        ref={ref}
        {...props}
      />
    );
  }
);

Card.displayName = 'Card';

export { Card };
