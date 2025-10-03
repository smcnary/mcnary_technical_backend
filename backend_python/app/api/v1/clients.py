"""
Client management endpoints
"""

from typing import List
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.auth import get_current_active_user, require_agency_admin
from app.schemas.client import ClientCreate, ClientUpdate, ClientResponse
from app.models.client import Client
from app.models.user import User

clients_router = APIRouter()

@clients_router.get("/", response_model=List[ClientResponse])
async def get_clients(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get all clients"""
    # Multi-tenant filtering based on user role
    query = db.query(Client)
    
    if current_user.is_system_admin():
        # System admin can see all clients
        pass
    elif current_user.is_agency_admin():
        # Agency admin can see their agency's clients
        query = query.filter(Client.agency_id == current_user.agency_id)
    else:
        raise HTTPException(status_code=403, detail="Insufficient permissions")
    
    clients = query.offset(skip).limit(limit).all()
    return clients

@clients_router.get("/{client_id}", response_model=ClientResponse)
async def get_client(
    client_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get a specific client"""
    client = db.query(Client).filter(Client.id == client_id).first()
    if not client:
        raise HTTPException(status_code=404, detail="Client not found")
    
    # Check permissions
    if not current_user.is_system_admin():
        if current_user.is_agency_admin():
            if client.agency_id != current_user.agency_id:
                raise HTTPException(status_code=403, detail="Not enough permissions")
        elif current_user.client_id and client.id != current_user.client_id:
            raise HTTPException(status_code=403, detail="Not enough permissions")
    
    return client

@clients_router.post("/", response_model=ClientResponse)
async def create_client(
    client_data: ClientCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_agency_admin)
):
    """Create a new client (agency admin only)"""
    # Create client
    client = Client(
        name=client_data.name,
        slug=client_data.slug,
        description=client_data.description,
        website_url=client_data.website_url,
        phone=client_data.phone,
        email=client_data.email,
        address=client_data.address,
        city=client_data.city,
        state=client_data.state,
        postal_code=client_data.postal_code,
        country=client_data.country,
        industry=client_data.industry,
        status=client_data.status,
        agency_id=client_data.agency_id
    )
    
    db.add(client)
    db.commit()
    db.refresh(client)
    
    return client

@clients_router.put("/{client_id}", response_model=ClientResponse)
async def update_client(
    client_id: str,
    client_data: ClientUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Update a client"""
    client = db.query(Client).filter(Client.id == client_id).first()
    if not client:
        raise HTTPException(status_code=404, detail="Client not found")
    
    # Check permissions
    if not current_user.is_system_admin():
        if current_user.is_agency_admin():
            if client.agency_id != current_user.agency_id:
                raise HTTPException(status_code=403, detail="Not enough permissions")
        elif current_user.client_id and client.id != current_user.client_id:
            raise HTTPException(status_code=403, detail="Not enough permissions")
    
    # Update fields
    update_data = client_data.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(client, field, value)
    
    db.commit()
    db.refresh(client)
    
    return client

@clients_router.delete("/{client_id}")
async def delete_client(
    client_id: str,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_agency_admin)
):
    """Delete a client (agency admin only)"""
    client = db.query(Client).filter(Client.id == client_id).first()
    if not client:
        raise HTTPException(status_code=404, detail="Client not found")
    
    db.delete(client)
    db.commit()
    
    return {"message": "Client deleted successfully"}
