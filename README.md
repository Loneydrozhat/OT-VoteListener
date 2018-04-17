<p align="center">
  <img width="600" height="194" src="https://otservers.org/img/votelistener.png">
</p>

# How it works
OT-VoteListener runs on the same machine as your OT server. It listens for vote notifications from [otservers.org](https://otservers.org), and then rewards the player who voted.

# Installation
Installation is very easy. Simply place `vote.php` somewhere in your public-facing web directory. Afterwards, generate a key from your [otservers.org](https://otservers.org) control panel.

## Configuring vote.php
Enter your key and OT server database connection information. A table named `player_votes` will be created automatically and used to store votes.
```
$key = ''
$dbUser = 'root'
$dbPass = 'toor'
$dbIP = 'localhost'
$dbPort = 3306
$dbDatabase = 'tibia_db'
```

## Adding the Lua script
Open `data/talkactions/talkactions.xml` and add the following line.
```
<talkaction words="!vote" script="vote.lua" />
```

Create the file `data/talkactions/scripts/votelistener.lua` and add the following code to it. This script allows users to check for queued vote rewards using the `!vote` command, and also handles which rewards the player will receive. If the player does not have any vote rewards queued, they will be given a link to vote for the server.

```
function giveReward(player)
    -- Enter code here for rewards
    -- Refer to the Github readme for examples
    -- https://github.com/otservers/OT-VoteListener
end

function onSay(player, words, param)
	local resultId = db.storeQuery("SELECT `votes` FROM `player_votes` WHERE `name` = " .. db.escapeString(player:getName()))
	if resultId == false then
		return false
	end

	local votes = result.getNumber(resultId, "votes")
	result.free(resultId)

	if votes == 0 then
        player:sendTextMessage(MESSAGE_EVENT_ADVANCE, "You do not have any pending vote rewards.")
        player:sendTextMessage(MESSAGE_EVENT_ADVANCE, "To vote, go to https://otservers.org/en/vote/YOUR_SERVER_ID_HERE")
		return false
	end
    
    db.query("UPDATE `player_votes` SET `votes` = 0 WHERE `name` = " .. db.escapeString(player:getName()))
    
    for vote=0,votes,1
    do
        giveReward(player)
        print("> " .. player:getName() .. " voted!")
        for _, targetPlayer in ipairs(Game.getPlayers()) do
            targetPlayer:sendPrivateMessage(MESSAGE_EVENT_ADVANCE, player:getName() .. " received rewards for voting!")
            targetPlayer:sendPrivateMessage(MESSAGE_EVENT_ADVANCE, "Say !vote for awesome rewards.")
        end
    end

	return false
end
```

## Customizing vote rewards
Customizing vote rewards is very easy. Simply add the rewards to the `giveReward()` function. 

### Example (give items)
Gives the player 1x Spike Sword and 1000x Gold Coins. Refer to `data/items/items.xml` for a list of all item IDs.
```
function giveReward(player)
    player:addItem(2383, 1)
    player:addItem(2148, 1000)
end
```
