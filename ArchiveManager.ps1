Add-Type -Path "$(${env:ProgramFiles(x86)})\WinSCP\WinSCPnet.dll" -ErrorAction Stop
$date = get-date -Format "dd-MM-yyyy_HH-mm"
$lcRemotePath = "/www/public/LedCameras"

# SSH
$username = "qbiview"
$secpasswd = ConvertTo-SecureString "****" -AsPlainText -Force
$credential = new-object -typename System.Management.Automation.PSCredential -argumentlist $username, $secpasswd
$snIP = "10.0.30.11"

function Get-WinScpChildItem ($WinScpSession, $BasePath) {
    $Directory = $WinScpSession.ListDirectory($BasePath)
    $MyCollection = @()

    foreach ($DirectoryEntry in $Directory.Files) {
    
        if (($DirectoryEntry.Name -ne '.') -and ($DirectoryEntry.Name -ne '..')) {
            $TempObject = New-Object System.Object
            
            if ($DirectoryEntry.IsDirectory) {
                $SavePath = $BasePath

                if ($BasePath -eq '/') {
                    $BasePath += "$($DirectoryEntry.Name)"
                }
                else {
                    $BasePath += "/$($DirectoryEntry.Name)"
                }
    
                $TempObject | Add-Member -MemberType NoteProperty -name 'Name' -Value $BasePath
                $TempObject | Add-Member -MemberType NoteProperty -name 'IsDirectory' -Value $true
                $MyCollection += $TempObject
                $MyCollection += Get-WinScpChildItem $WinScpSession $BasePath
                $BasePath = $SavePath
            }
            else {
                $TempObject | Add-Member -MemberType NoteProperty -name 'Name' -Value "$BasePath/$DirectoryEntry"
                $TempObject | Add-Member -MemberType NoteProperty -name 'IsDirectory' -Value $false
                $MyCollection += $TempObject
            }
        }
    }
    
    return $MyCollection
}

$sessionOptions = New-Object WinSCP.SessionOptions -Property @{
    Protocol   = [WinSCP.Protocol]::ftp
    HostName   = "10.0.30.11"
    PortNumber = 21
    UserName   = "qbiview"
    Password   = "*****"
}

$session = New-Object WinSCP.Session
$session.Open($sessionOptions)
$transferOptions = New-Object WinSCP.TransferOptions
$transferOptions.TransferMode = [WinSCP.TransferMode]::Binary
$transferOptions.OverwriteMode = [WinSCP.OverwriteMode]::Overwrite
$transferOptions.FilePermissions = New-Object WinSCP.FilePermissions
$transferOptions.FilePermissions.Octal = "777"

# MOVE TO ARCHIVE
$ss = Get-WinSCPChildItem -WinScpSession $session -BasePath "$lcRemotePath/ss"
$toArchive = ($ss | ? { (($_.IsDirectory -eq $false) -and ($_.Name -like "*CameraScreenshot*")) }).Name

Foreach ($img in $toArchive) {
    $a = (Split-Path -Leaf -Path $img).Replace(".jpg","")
    $file = -join($a, "_", $date, ".jpg")
    $sn = Split-Path -Leaf -Path (Split-Path -Parent -Path $img) 

    if (!($session.ListDirectory("$lcRemotePath").Files.Name -contains "archive")) {
        $session.CreateDirectory("$lcRemotePath/archive")
        Write-host "Creating archive"
    }
    
    if (!($session.ListDirectory("$lcRemotePath/archive").Files.Name -contains $sn)) {
        $session.CreateDirectory("$lcRemotePath/archive/$sn")
        Write-host "Creating directory for computer $sn on server"
        $session.PutFiles("C:\SN_Scripts\ArchiveManager\index.php","$lcRemotePath/archive/$sn/", $False, $transferOptions) | out-null

    }

    $session.MoveFile($img,"$lcRemotePath/archive/$sn/$file")
    Write-Host "Moving file '$img' to '$lcRemotePath/archive/$sn'"
}

# SET FILE PERMISION
Write-host "Setting permisions to archive files"
New-SSHSession -ComputerName $snIP -Credential $credential -ConnectionTimeout 120 -force
$getSSHSessionId = (Get-SSHSession | Where-Object { $_.Host -eq $snIP }).SessionId
(Invoke-SSHCommand -SessionId $getSSHSessionId -Command "find www/public/LedCameras | xargs chmod --changes -R 777").output
Remove-SSHSession -SessionId $getSSHSessionId

""

# CLEAN OLD ARCHIVE FILES
$archDirs = (Get-WinSCPChildItem -WinScpSession $session -BasePath "$lcRemotePath/archive" | ? { $_.IsDirectory -eq $true }).Name

foreach ($dir in $archDirs) {
    ($session.ListDirectory("$dir")).Files | ? { $_.Length -gt 1 } | % {
        
        if ($_.Name -ne "index.php") {
            
            if ($_.LastWriteTime -lt ((get-date).AddDays(-178))) {
                $del = $session.RemoveFile($_.FullName)
                
                if ($del.Error -eq $null) {
                    Write-Host "Removing file '$($_.Name)'"
                }
                else {
                    Write-Host "Error while removeing '$($_.Name)': $del.Error"
                }
            }
        }
    }
}

$session.Dispose()