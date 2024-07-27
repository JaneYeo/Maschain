
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
    const clientId = '99f55f11db1771b614b8650d9069efceda923e04472114d4ba63d7ba7996307c';
    const clientSecret = 'sk_ebf43e9b8dca38c04b566f808f1492bcb4ff59fcaf3a36251890ed844293cc23';

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
                            //if(data.ic==users.ic){
                                await createUserWallet(user, data);
                            //}
                            
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