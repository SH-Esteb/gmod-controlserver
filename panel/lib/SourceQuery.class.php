<?php
class SourceQuery
{
	/********************************************************************************
	 * Class written by xPaw <xpaw.crannk.de>
	 *
	 * GitHub: https://github.com/xPaw/PHP-Source-Query-Class
	 *
	 ********************************************************************************
	 * INFORMATION:
	 * - If server is HLTV, bots == spectators (You can know that by comparing 'Dedicated' == 'p')
	 * - GetPlayers() for HLTV returns players on game server, and their time will be always 1 second
	 * - Source RCON uses TCP, so it requires different connection and protocol for it
	 ********************************************************************************/
	
	protected $Resource;
	protected $Connected;
	protected $RconPassword;
	protected $RconChallenge;
	protected $Challenge;
	protected $IsSource;
	protected $RconId;
	protected $Temp;
	
	public function __destruct( )
	{
		$this->Disconnect( );
	}
	
	public function Connect( $Ip, $Port = 27015 )
	{
		$this->Disconnect( );
		$this->RconChallenge = 0;
		$this->RconPassword  = 0;
		$this->Challenge     = 0;
		$this->IsSource      = 0;
		$this->RconId        = 0;
		
		if( ( $this->Resource = @FSockOpen( 'udp://' . GetHostByName( $Ip ), (int)$Port ) ) )
		{
			$this->Connected = true;
			Socket_Set_TimeOut( $this->Resource, 3 );
			
		/*	if( !$this->Ping( ) ) // TODO: Ping does not work in TF2
			{
				$this->Disconnect( );
				
				throw new SQueryException( "This server is not responding to Source Query Protocol." );
			}	*/
		}
		else
		{
			throw new SQueryException( "Can't connect to the server." );
		}
		
		return $this->Connected;
	}
	
	public function SetRconPassword( $Password )
	{
		$this->RconPassword = $Password;
		
		if( $this->RconChallenge || !$Password )
		{
			return false;
		}
		
		$this->WriteData( 'challenge rcon' );
		$Data = $this->ReadData( );
		
		if( Empty( $Data ) )
		{
			// Little bit stupid workaround
			$this->IsSource = true;
		}
		else if( $this->_CutByte( $Data, 18 ) != "\xFF\xFF\xFF\xFFchallenge rcon" )
		{
			return false;
		}
		
		$this->RconChallenge = Trim( $Data );
		
		return true;
	}
	
	public function Disconnect( )
	{
		if( $this->Connected )
		{
			$this->Connected = false;
			
			fclose( $this->Resource );
		}
	}
	
	private function Ping( )
	{
		$this->WriteData( 'i' );
		$Type = $this->ReadData( );
		
		if( $Type && $Type[ 4 ] == 'j' )
		{
			$this->IsSource = ( $Type[ 5 ] == '0' );
			
			return true;
		}
		
		return false;
	}
	
	public function GetInfo( )
	{
		if( !$this->Connected )
		{
			return false;
		}
		
		$this->WriteData( 'TSource Engine Query' );
		$Data = $this->ReadData( );
		
		$Type = $this->_CutByte( $Data, 5 );
		$Type = $Type[ 4 ];
		
		if( $Type == 'm' )//S2A_INFO GoldSource format (obsolete)
		{
			$this->Temp = $this->ReadData( );

                    if( $this->Temp[ 4 ] == 'D' )//S2A_PLAYER packet, ignore/drop it, read next packet 
			{ // used in DProto: 3 packets in a row:
                          // obsolete S2A_INFO GoldSource, S2A_PLAYER, modern S2A_INFO Source 
                        $this->Temp = $this->ReadData( );
                    }

                    if( $this->Temp[ 4 ] == 'I' )//S2A_INFO Source format (modern)
			{
				$Data = $this->Temp;
				$Type = $this->_CutByte( $Data, 5 );
				$Type = $Type[ 4 ];
			}
			$this->Temp = null;

			// Seriously, don't look at me like this, blame Valve!!
		}
		
		if( $Type == 'm' ) // Old GoldSrc protocol, HLTV still uses it
		{
			$Server[ 'Address' ]    = $this->_CutString( $Data );
			$Server[ 'HostName' ]   = $this->_CutString( $Data );
			$Server[ 'Map' ]        = $this->_CutString( $Data );
			$Server[ 'ModDir' ]     = $this->_CutString( $Data );
			$Server[ 'ModDesc' ]    = $this->_CutString( $Data );
			$Server[ 'Players' ]    = $this->_CutNumber( $Data );
			$Server[ 'MaxPlayers' ] = $this->_CutNumber( $Data );
			$Server[ 'Protocol' ]   = $this->_CutNumber( $Data );
			$Server[ 'Dedicated' ]  = $this->_CutByte( $Data );
			$Server[ 'Os' ]         = $this->_CutByte( $Data );
			$Server[ 'Password' ]   = $this->_CutNumber( $Data );
			$Server[ 'IsMod' ]      = $this->_CutNumber( $Data );
			
			if( $Server[ 'IsMod' ] ) // TODO: Needs testing
			{
				$Mod[ 'Url' ]        = $this->_CutString( $Data );
				$Mod[ 'Download' ]   = $this->_CutString( $Data );
				$this->_CutByte( $Data ); // NULL byte
				$Mod[ 'Version' ]    = $this->_CutNumber( $Data );
				$Mod[ 'Size' ]       = $this->_CutNumber( $Data );
				$Mod[ 'ServerSide' ] = $this->_CutNumber( $Data );
				$Mod[ 'CustomDLL' ]  = $this->_CutNumber( $Data );
			}
			
			$Server[ 'Secure' ]   = $this->_CutNumber( $Data );
			$Server[ 'Bots' ]     = $this->_CutNumber( $Data );
			
			if( isset( $Mod ) )
			{
				$Server[ 'Mod' ] = $Mod;
			}
			
			return $Server;
		}
		else if( $Type != 'I' )
		{
			return false;
		}
		
		$Server[ 'Protocol' ]   = $this->_CutNumber( $Data );
		$Server[ 'HostName' ]   = $this->_CutString( $Data );
		$Server[ 'Map' ]        = $this->_CutString( $Data );
		$Server[ 'ModDir' ]     = $this->_CutString( $Data );
		$Server[ 'ModDesc' ]    = $this->_CutString( $Data );
		$Server[ 'AppID' ]      = $this->_UnPack( 'S', $this->_CutByte( $Data, 2 ) );
		$Server[ 'Players' ]    = $this->_CutNumber( $Data );
		$Server[ 'MaxPlayers' ] = $this->_CutNumber( $Data );
		$Server[ 'Bots' ]       = $this->_CutNumber( $Data );
		$Server[ 'Dedicated' ]  = $this->_CutByte( $Data );
		$Server[ 'Os' ]         = $this->_CutByte( $Data );
		$Server[ 'Password' ]   = $this->_CutNumber( $Data );
		$Server[ 'Secure' ]     = $this->_CutNumber( $Data );
		
		if( $Server[ 'AppID' ] == 2400 ) // The Ship
		{
			$Server[ 'GameMode' ]     = $this->_CutNumber( $Data );
			$Server[ 'WitnessCount' ] = $this->_CutNumber( $Data );
			$Server[ 'WitnessTime' ]  = $this->_CutNumber( $Data );
		}
		
		$Server[ 'Version' ] = $this->_CutString( $Data );
		
		// EXTRA DATA FLAGS
		$Flags = $this->_CutNumber( $Data );
		
		// The server's game port #
		if( $Flags & 0x80 )
		{
			$Server[ 'EDF' ][ 'GamePort' ] = $this->_UnPack( 'S', $this->_CutByte( $Data, 2 ) );
		}
		
		// The server's SteamID
		if( $Flags & 0x10 )
		{
			// TODO: long long ...
			$this->_CutByte( $Data, 8 );
		}
		
		// The spectator port # and then the spectator server name
		if( $Flags & 0x40 )
		{
			$Server[ 'EDF' ][ 'SpecPort' ] = $this->_UnPack( 'S', $this->_CutByte( $Data, 2 ) );
			$Server[ 'EDF' ][ 'SpecName' ] = $this->_CutString( $Data );
		}
		
		// The game tag data string for the server
		if( $Flags & 0x20 )
		{
			$Server[ 'EDF' ][ 'GameTags' ] = $this->_CutString( $Data );
		}
		
		// The Steam Application ID again + several 0x00 bytes
	/*	if( $Flags & 0x01 )
		{
			$this->_UnPack( 'S', $this->_CutByte( $Data, 2 ) );
		}	*/
		
		return $Server;
	}
	
	public function GetPlayers( )
	{
		if( !$this->Connected )
		{
			return false;
		}
		
		$Challenge = $this->GetChallenge( 'U' );
		
		if( $this->Temp )
		{
			$Data = $this->Temp;
			$this->Temp = null;
		}
		else if( !$Challenge )
		{
			return false;
		}
		
		if( !isset( $Data ) )
		{
			$this->WriteData( 'U' . $Challenge );
			$Data = $this->ReadData( );
		}
		
		if( $this->_CutByte( $Data, 5 ) != "\xFF\xFF\xFF\xFFD" )
		{
			return false;
		}
		
		$Count = $this->_CutNumber( $Data );
		
		if( $Count <= 0 ) // No players
		{
			return false;
		}
		
		for( $i = 0; $i < $Count; $i++ )
		{
			$this->_CutByte( $Data ); // PlayerID, is it just always 0?
			
			$Players[ $i ][ 'Name' ]    = $this->_CutString( $Data );
			$Players[ $i ][ 'Frags' ]   = $this->_UnPack( 'L', $this->_CutByte( $Data, 4 ) );
			$Time                       = (int)$this->_UnPack( 'f', $this->_CutByte( $Data, 4 ) );
			$Players[ $i ][ 'IntTime' ] = $Time;
			$Players[ $i ][ 'Time' ]    = GMDate( ( $Time > 3600 ? "H:i:s" : "i:s" ), $Time );
		}
		
		return $Players;
	}
	
	public function GetRules( )
	{
		if( !$this->Connected )
		{
			return false;
		}
		
		$Challenge = $this->GetChallenge( 'V' );
		
		if( $this->Temp )
		{
			$Data = $this->Temp;
			$this->Temp = null;
		}
		else if( !$Challenge )
		{
			return false;
		}
		
		if( !isset( $Data ) )
		{
			$this->WriteData( 'V' . $Challenge );
			$Data = $this->ReadData( );
		}
		
		if( $this->_CutByte( $Data, 5 ) != "\xFF\xFF\xFF\xFFE" )
		{
			return false;
		}
		
		$Count = $this->_UnPack( 'S', $this->_CutByte( $Data, 2 ) );
		
		if( $Count <= 0 ) // Can this even happen?
		{
			return false;
		}
		
		$Rules = Array( );
		
		for( $i = 0; $i < $Count; $i++ )
		{
			$Rules[ $this->_CutString( $Data ) ] = $this->_CutString( $Data );
		}
		
		return $Rules;
	}
	
	private function GetChallenge( $Char )
	{
		if( $this->Challenge )
		{
			return $this->Challenge;
		}
		
		$this->WriteData( "{$Char}\xFF\xFF\xFF\xFF" );
		$Data = $this->Temp = $this->ReadData( );
		
		return $this->Challenge;
	}
	
	// ==========================================================
	// RCON
	public function Rcon( $Command, $DoWeCareAboutResult = true )
	{
		if( !$this->Connected )
		{
			return false;
		}
		else if( $this->IsSource )
		{
			throw new SQueryException( "Source RCON Protocol is not supported yet." );
		}
		else if( !$this->RconPassword || !$this->RconChallenge )
		{
			throw new SQueryException( "No rcon password is specified." );
		}
		
		$this->WriteData( 'rcon ' . $this->RconChallenge . ' "' . $this->RconPassword . '" ' . $Command );
		
		if( !$DoWeCareAboutResult )
		{
			return true;
		}
		
		Socket_Set_TimeOut( $this->Resource, 1 );
		
		$Buffer = "";
		
		while( $Type = FRead( $this->Resource, 5 ) )
		{
			if( Ord( $Type[ 0 ] ) == 254 ) // More than one datagram
			{
				$Data = SubStr( $this->_ReadSplitPackets( 3 ), 4 );
			}
			else
			{
				$Status = Socket_Get_Status( $this->Resource );
				$Data   = FRead( $this->Resource, $Status[ 'unread_bytes' ] );
			}
			
			$Buffer .= RTrim( $Data, "\0" );
		}
		
		Socket_Set_TimeOut( $this->Resource, 3 );
		
		return $Buffer;
	}
	
	// ==========================================================
	// DATA WORKERS
	private function WriteData( $Command )
	{
		$Command = "\xFF\xFF\xFF\xFF" . $Command . "\x00";
		// return result of write - if failed or write not all bytes  return false, else - true
		return StrLen( $Command ) !== fwrite( $this->Resource, $Command, StrLen( $Command ) );
	}
	
	private function ReadData( )
	{
		$Data = FRead( $this->Resource, 1 );
		
		switch( Ord( $Data ) )
		{
			case 255: // Just one datagram
				$Status = Socket_Get_Status( $this->Resource );
				$Data  .= FRead( $this->Resource, $Status[ 'unread_bytes' ] );
				
				break;
			
			case 254: // More than one datagram
				$Data = $this->_ReadSplitPackets( 7 );
				break;
			
			case 0:
				return false;
		}
		
		if( !$Data )
		{
			return false;
		}
		
		if( $Data[ 4 ] == 'l' )
		{
			$Temp = RTrim( SubStr( $Data, 5, 42 ) ); // TODO:
			
			if( $Temp == "You have been banned from this server." )
			{
				throw new SQueryException( $Temp );
				
				return false;
			}
		}
		else if( $Data[ 4 ] == 'A' && SubStr( $Data, 0, 5 ) == "\xFF\xFF\xFF\xFFA" )
		{
			// We got challenge!
			$this->Challenge = SubStr( $Data, 5 );
			
			return false;
		}
		
		return $Data;
	}
	
	private function _ReadSplitPackets( $BytesToRead )
	{
		FRead( $this->Resource, $BytesToRead );
		
		// The 9th byte tells us the datagram id and the total number of datagrams
		$Data = FRead( $this->Resource, 1 );
		
		// We need to evaluate this in bits (so convert to binary)
		$Bits = SPrintF( "%08b", Ord( $Data ) );
		
		// The low bits denote the total number of datagrams (1-based)
		$Count = BinDec( SubStr( $Bits, -4 ) );
		
		// The high bits denote the current datagram id
		$x = BinDec( SubStr( $Bits, 0, 4 ) );
		
		// The rest is the datagram content.
		$Status = Socket_Get_Status( $this->Resource );
		$Datagrams[ $x ] = FRead( $this->Resource, $Status[ 'unread_bytes' ] );
		
		// Repeat this process for each datagram
		// We've already done the first one, so $i = 1 to start at the next
		for( $i = 1; $i < $Count; $i++ )
		{
			// Skip the header.
			FRead( $this->Resource, 8 );
			
			// Evaluate the 9th byte.
			$Data = FRead( $this->Resource, 1 );
			$x = BinDec( SubStr( SPrintF( "%08b", Ord( $Data ) ), 0, 4 ) );
			
			// Read the datagram content
			$Status = Socket_Get_Status( $this->Resource );
			$Datagrams[ $x ] = FRead( $this->Resource, $Status[ 'unread_bytes' ] );
		}
		
		$Data = "";
		for( $i = 0; $i < $Count; $i++ )
		{
			$Data .= $Datagrams[ $i ];
		}
		
		return $Data;
	}
	
	private function _CutNumber( &$Buffer )
	{
		return Ord( $this->_CutByte( $Buffer ) );
	}
	
	private function _CutByte( &$Buffer, $Length = 1 )
	{
		$String = SubStr( $Buffer, 0, $Length );
		$Buffer = SubStr( $Buffer, $Length );
		
		return $String;
	}
	
	private function _CutString( &$Buffer )
	{
		$Length = StrPos( $Buffer, "\x00" );
		
		if( $Length === FALSE )
		{
		//	$Length = StrLen( $Buffer );
			
			$String = $Buffer;
			$Buffer = "";
		}
		else
		{
			$String = $this->_CutByte( $Buffer, $Length );
			// remove null-byte (terminate for string) from buffer
			$this->_CutByte( $Buffer, 1 );
		}
		
		return $String;
	}
	
	private function _UnPack( $Format, $Buffer )
	{
		List( , $Buffer ) = UnPack( $Format, $Buffer );
		
		return $Buffer;
	}
}

class SQueryException extends Exception
{
	// Isn't it funny to have empty class just to have fancy exception name
}
