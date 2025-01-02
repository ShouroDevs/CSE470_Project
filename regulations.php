<?php

include("./header.php");
include 'fetchuserinfo.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="regulations.css">
    <title>Parking Regulations</title>
</head>
<body>
    <header>
        <h1>City Parking Regulations</h1>
        <p>Comply with local parking and traffic laws to ensure smooth and safe driving</p>
    </header>

    <div class="container">
        <section>
            <h2>General Parking Rules</h2>
            <div class="regulation-card">
                <h3>1. Parking in Designated Areas Only</h3>
                <p>Vehicles should only park in areas specifically marked for parking. Unauthorized parking may result in fines or towing.</p>
                <p class="updated">Last Updated: January 2025</p>
            </div>
            <div class="regulation-card">
                <h3>2. No Parking During Specific Hours</h3>
                <p>Some parking spaces are restricted during certain hours for street cleaning or other activities. Check posted signs before parking.</p>
                <p class="updated">Last Updated: December 2024</p>
            </div>
            <div class="regulation-card">
                <h3>3. Disabled Parking Spots</h3>
                <p>Only vehicles displaying the proper disabled parking permit are allowed to park in designated disabled parking spaces.</p>
                <p class="updated">Last Updated: November 2024</p>
            </div>
            <div class="regulation-card">
                <h3>4. No Double Parking</h3>
                <p>Double parking is strictly prohibited and can result in a fine or towing of the vehicle causing the obstruction.</p>
                <p class="updated">Last Updated: January 2025</p>
            </div>
        </section>

        <section>
            <h2>Traffic Regulations</h2>
            <div class="regulation-card">
                <h3>5. Speed Limits in Parking Areas</h3>
                <p>Ensure you follow the posted speed limits within parking garages and lots. Speeding can lead to accidents and fines.</p>
                <p class="updated">Last Updated: January 2025</p>
            </div>
            <div class="regulation-card">
                <h3>6. No Overtaking in Parking Lots</h3>
                <p>Overtaking in parking lots can be dangerous. Always drive cautiously and avoid risky maneuvers in these areas.</p>
                <p class="updated">Last Updated: December 2024</p>
            </div>
            <div class="regulation-card">
                <h3>7. Parking Near Crosswalks</h3>
                <p>It is illegal to park within 5 meters of a crosswalk. This ensures pedestrian safety and visibility for both drivers and pedestrians.</p>
                <p class="updated">Last Updated: December 2024</p>
            </div>
            <div class="regulation-card">
                <h3>8. Reserved Parking for Emergency Vehicles</h3>
                <p>Parking spots marked for emergency vehicles (police, fire trucks, ambulances) are strictly reserved for those vehicles. Unauthorized parking in these spots may result in heavy fines.</p>
                <p class="updated">Last Updated: January 2025</p>
            </div>
        </section>

        <section>
            <h2>Special Regulations</h2>
            <div class="regulation-card">
                <h3>9. Electric Vehicle Charging Stations</h3>
                <p>Parking spots with charging stations are reserved for electric vehicles. Non-electric vehicles will be fined if parked in these spots.</p>
                <p class="updated">Last Updated: January 2025</p>
            </div>
            <div class="regulation-card">
                <h3>10. Carpool Lane Parking</h3>
                <p>Some parking areas allow parking for vehicles with multiple occupants. Make sure to follow the specific rules for such spots to avoid fines.</p>
                <p class="updated">Last Updated: January 2025</p>
            </div>
            <div class="regulation-card">
                <h3>11. Parking Fees</h3>
                <p>Always check the signage for parking fees. Fees vary based on location and time of day. Non-payment may result in fines or ticketing.</p>
                <p class="updated">Last Updated: December 2024</p>
            </div>
            <div class="regulation-card">
                <h3>12. Parking for Trucks and Commercial Vehicles</h3>
                <p>Trucks and commercial vehicles may only park in designated areas. Unauthorized parking in regular spaces will result in fines.</p>
                <p class="updated">Last Updated: November 2024</p>
            </div>
        </section>

        <section>
            <h2>Dynamic Parking Regulation Updates</h2>
            <div class="dynamic-regulations">
                <p>City officials may implement new parking regulations based on traffic studies, construction activities, or other city planning needs. Always stay updated with the latest parking and traffic information through local city websites or mobile apps.</p>
                <p>Recent Changes: Increased parking fines for unauthorized parking in electric vehicle charging spaces.</p>
            </div>
        </section>
    </div>

    <footer>
        <p>&copy; 2025 City Parking Authority. All rights reserved.</p>
    </footer>
</body>
</html>
