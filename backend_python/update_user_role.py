"""
Script to update user role to ROLE_SYSTEM_ADMIN
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from sqlalchemy.orm import sessionmaker
from app.core.database import engine
from app.models.user import User

def update_user_role():
    """Update user role to ROLE_SYSTEM_ADMIN"""
    
    # Create session
    Session = sessionmaker(bind=engine)
    db = Session()
    
    try:
        # Find the user
        user = db.query(User).filter(User.email == "smcnary@live.com").first()
        if not user:
            print("User smcnary@live.com not found")
            return None
        
        print(f"Found user: {user.email}")
        print(f"Current role: {user.role}")
        
        # Update role to ROLE_SYSTEM_ADMIN
        user.role = User.ROLE_SYSTEM_ADMIN
        
        db.commit()
        db.refresh(user)
        
        print(f"Updated user role to: {user.role}")
        print(f"User is now system admin: {user.is_system_admin()}")
        
        return user
        
    except Exception as e:
        db.rollback()
        print(f"Error updating user role: {e}")
        raise
    finally:
        db.close()

if __name__ == "__main__":
    update_user_role()
