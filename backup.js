
const fetch = require('node-fetch');
const fs = require('fs').promises;
const path = require('path');

async function fetchDatabase() {
    try {
        const data = await fs.readFile(path.join(__dirname, 'database.json'), 'utf8');
        console.log('Database loaded successfully');
        return JSON.parse(data);
    } catch (error) {
        console.error('Error reading database JSON file:', error);
    }
}

async function fetchUserdb() {
    try {
        const data = await fs.readFile(path.join(__dirname, 'Hackathon', 'users.json'), 'utf8');
        console.log('Users database loaded successfully');
        return JSON.parse(data);
    } catch (error) {
        console.error('Error reading users JSON file:', error);
    }
}

async function createUserWallet(user, data) {
    const url = 'https://service-testnet.maschain.com/api/wallet/create-user';
    const clientId = '1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241';
    const clientSecret = 'sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b';

    const payload = {
        name: user.name,
        email: user.email,
        ic: data.ic,
        phone: data.phonenumber
    };

    try {
        console.log('Attempting to create wallet for user:', user.name);
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'client_id': clientId,
                'client_secret': clientSecret,
                'content-type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        console.log('Wallet creation result:', result);
    } catch (error) {
        console.error('Error creating wallet:', error);
    }
}

async function main() {
    const database = await fetchDatabase();
    const users = await fetchUserdb();

    if (database && users) {
        console.log('Both database and users loaded');
        for (let data of database) {
            for(let user of users){
                //if(data.ic==users.ic){
                    console.log('Checking data:', data);
                    if (data.race === "Malay" && data.category === "B40" && data.alive === true) {
                        console.log('Data meets criteria, creating wallet for each user');
                        for (let user of users) {
                            console.log(user.ic);
                            if(data.ic==user.ic){
                                await createUserWallet(user, data);
                            }
                            
                        }
                    } else {
                        console.log('Data does not meet criteria');
                    }
               // }
               
            }
            
        }
    } else {
        //console.log('Either database or users failed to load');
    }
}

main();