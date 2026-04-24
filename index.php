<?php

$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$action = $_GET['a'] ?? '';

// 🔒 브라우저 차단
if (strpos($ua, 'Roblox') === false && !$action) {
    http_response_code(403);
    die("
    <html>
    <body style='background:#0f0f0f;color:white;text-align:center;margin-top:20%'>
    <h2>Access to this site has been restricted.</h2>
    <p>Error 001</p>
    <p>kimtimny0414@gmail.com</p>
    </body>
    </html>
    ");
}

// 토큰 폴더
$dir = __DIR__ . "/t/";
if (!is_dir($dir)) mkdir($dir);

// 🔑 토큰 발급
if ($action === "get") {
    $token = bin2hex(random_bytes(8));
    file_put_contents($dir . $token, time());
    echo $token;
    exit;
}

// 🔒 로드
if ($action === "load") {

    $token = $_GET['t'] ?? '';
    $file = $dir . $token;

    if (!$token || !file_exists($file)) die("");

    if (time() - file_get_contents($file) > 5) {
        unlink($file);
        die("");
    }

    unlink($file);

    header("Content-Type: text/plain");

    // 🔥 네 Lua 코드 그대로 삽입
    $lua = <<<'LUA'
local you_hwid = gethwid()

local function domain_control(x)
    return game:HttpGet("https://s"..math.random(1,4)..".ntt-system.xyz/?type=data&domain=ffa-hub&hwid="..x)
end

local TweenService = game:GetService("TweenService")

local ScreenGui = Instance.new("ScreenGui", game.CoreGui)
ScreenGui.Name = "FFAHUB"

local function createLoader(text)
    local Loader = Instance.new("Frame", ScreenGui)
    Loader.Size = UDim2.new(1,0,1,0)
    Loader.BackgroundColor3 = Color3.fromRGB(15,15,15)

    local Label = Instance.new("TextLabel", Loader)
    Label.Size = UDim2.new(1,0,0,50)
    Label.Position = UDim2.new(0,0,0.6,0)
    Label.Text = text or "Loading..."
    Label.TextColor3 = Color3.new(1,1,1)
    Label.BackgroundTransparency = 1
    Label.Font = Enum.Font.GothamBold
    Label.TextSize = 16

    local Spinner = Instance.new("Frame", Loader)
    Spinner.Size = UDim2.new(0,40,0,40)
    Spinner.Position = UDim2.new(0.5,-20,0.45,-20)
    Spinner.BackgroundColor3 = Color3.fromRGB(0,170,127)
    Spinner.BorderSizePixel = 0

    Instance.new("UICorner", Spinner).CornerRadius = UDim.new(1,0)

    task.spawn(function()
        while Loader.Parent do
            local tween = TweenService:Create(
                Spinner,
                TweenInfo.new(0.8, Enum.EasingStyle.Linear),
                {Rotation = Spinner.Rotation + 180}
            )
            tween:Play()
            tween.Completed:Wait()
        end
    end)

    return Loader
end

local FirstLoad = createLoader("Loading Hub...")
task.wait(2)
FirstLoad:Destroy()

local Main = Instance.new("Frame", ScreenGui)
Main.Size = UDim2.new(0, 320, 0, 200)
Main.Position = UDim2.new(0.5, -160, 0.5, -100)
Main.BackgroundColor3 = Color3.fromRGB(20, 20, 20)
Main.BorderSizePixel = 0
Instance.new("UICorner", Main).CornerRadius = UDim.new(0,10)

print("REAL SCRIPT LOADED")
LUA;

    // base64 숨김
    $encoded = base64_encode($lua);

    echo "loadstring(game:HttpGet('https://your-site.com/index.php?a=dec&d={$encoded}'))()";
    exit;
}

// 🔓 디코더
if ($action === "dec") {
    header("Content-Type: text/plain");
    echo base64_decode($_GET['d'] ?? '');
    exit;
}

die("");
