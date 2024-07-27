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
        
        if (result.status === 200 && result.result && result.result.wallet) {
            const recipientAddress = result.result.wallet.wallet_address;
            console.log("Recipient ID: " + recipientAddress);
            await transferTokens(recipientAddress);
        } else {
            console.error('Wallet creation failed or unexpected response structure');
        }
    } catch (error) {
        console.error('Error creating wallet:', error);
    }
}

async function transferTokens(recipientAddress) {
    const walletAddress = "0xebD0a58Ea912C39d251E8C215cfc9af7c29d6228"; 
    const amount = 100;
    const contractAddress = "0xa7e30c1c27BB46932Fc1466FF472e134d689B4D6";
    const callbackUrl = "https://postman-echo.com/post";
    
    const dataInput = {
        wallet_address: walletAddress,
        to: recipientAddress,
        amount: amount,
        contract_address: contractAddress,
        callback_url: callbackUrl
    };
    
    const key = "1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241";
    const secret = "sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b";
    
    const apiUrl = 'https://service-testnet.maschain.com/api/token/token-transfer';
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'client_id': key,
                'client_secret': secret,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dataInput)
        });

        const data = await response.json();
        console.log('Token transfer result:', JSON.stringify(data, null, 2));
    } catch (error) {
        console.error('Error transferring tokens:', error);
    }
}

async function main() {
    const database = await fetchDatabase();
    const users = await fetchUserdb();

    if (database && users) {
        console.log('Both database and users loaded');
        for (let data of database) {
            console.log('Checking data:', data);
            if (data.race === "Malay" && data.category === "B40" && data.alive === true) {
                console.log('Data meets criteria, creating wallet for matching user');
                const matchingUser = users.find(user => user.ic === data.ic);
                if (matchingUser) {
                    await createUserWallet(matchingUser, data);
                } else {
                    console.log('No matching user found for IC:', data.ic);
                }
            } else {
                console.log('Data does not meet criteria');
            }
        }
    } else {
        console.log('Either database or users failed to load');
    }
}

main().catch(error => console.error('Unhandled error in main:', error));