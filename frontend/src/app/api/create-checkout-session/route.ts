import { NextRequest, NextResponse } from 'next/server';
import Stripe from 'stripe';

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
  apiVersion: '2024-12-18.acacia',
});

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const {
      serviceType,
      price,
      customerEmail,
      customerName,
      companyName,
      website,
      industry,
      goals,
      competitors,
      monthlyBudget,
      notes,
    } = body;

    // Validate required fields
    if (!serviceType || !price || !customerEmail) {
      return NextResponse.json(
        { error: 'Missing required fields' },
        { status: 400 }
      );
    }

    // Create Stripe checkout session
    const session = await stripe.checkout.sessions.create({
      payment_method_types: ['card'],
      line_items: [
        {
          price_data: {
            currency: 'usd',
          product_data: {
            name: serviceType === "audit" ? "SEO Audit" : "Full SEO Service",
            description: serviceType === "audit" 
              ? `Comprehensive SEO audit for ${companyName}` 
              : `Complete SEO transformation for ${companyName}`,
            metadata: {
              serviceType,
              companyName,
              website,
              industry,
              goals: JSON.stringify(goals),
              competitors,
              monthlyBudget,
              notes,
            },
          },
            unit_amount: price * 100, // Convert to cents
          },
          quantity: 1,
        },
      ],
      mode: 'payment',
      customer_email: customerEmail,
      success_url: `${process.env.NEXT_PUBLIC_BASE_URL || 'http://localhost:3000'}/audit-wizard?success=true&session_id={CHECKOUT_SESSION_ID}`,
      cancel_url: `${process.env.NEXT_PUBLIC_BASE_URL || 'http://localhost:3000'}/audit-wizard?canceled=true`,
      metadata: {
        serviceType,
        customerName,
        companyName,
        website,
        industry,
        goals: JSON.stringify(goals),
        competitors,
        monthlyBudget,
        notes,
      },
      shipping_address_collection: {
        allowed_countries: ['US', 'CA'],
      },
      billing_address_collection: 'required',
    });

    return NextResponse.json({ sessionId: session.id });
  } catch (error) {
    console.error('Error creating checkout session:', error);
    return NextResponse.json(
      { error: 'Failed to create checkout session' },
      { status: 500 }
    );
  }
}
