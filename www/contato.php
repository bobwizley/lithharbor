<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Contato Maple</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="estilos.css" />
<?php include 'ga.php'; ?>
</head>
<body background="images/background.jpg" leftmargin="25" topmargin="25" rightmargin="0" bottommargin="0">
<embed src="http://www.thejasoneffect.net/music/MapleStory/MemoriesofOssyria/Orbis.mp3" autostart="true" hidden="true"></embed>
<table width="778" border="0" align="left" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="3"><!-- Inicio do topo -->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="278" height="100" valign="middle" background="images/bg_01tit.png">
          </td>
          <td width="500" height="100" valign="middle" background="images/bg_01.png">
          <div align="center"> </div>          </td>
        </tr>
        <tr>
          <td height="20" background="images/bg_02.jpg" class="texto">
          &nbsp;&nbsp;&nbsp;<a href="index.php"><strong>Home</strong></a>
          &nbsp;&nbsp;&nbsp;&nbsp;<a href="http://lithharbor.net/forum"><strong>F&oacute;rum</strong></a>
          &nbsp;&nbsp;&nbsp;&nbsp;<strong><a href="contato.php">Contato</a></strong></td>
          <td height="20" background="images/bg_02.jpg" class="texto"><div align="right"></div>
          </td>
        </tr>
      </table>
      <!-- Fim do topo -->
    </td>
  </tr>
  <tr>
    <td height="5"></td>
    <td height="5"></td>
    <td height="5"></td>
  </tr>
  <tr>
    <?php include "menu.php"; ?>
    <td width="478" valign="top">
      <table width="476" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="10" background="images/meio_01.gif"></td>
        </tr>
        <tr>
          <td height="577" valign="top" background="images/meio_02.gif"><!-- Inicio do conteudo -->
            <table width="460" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="545" valign="top">
                  <div align="center" class="titulo2">Contato</div>
                  <?php if (isset($_GET['msg'])) : ?>
                  <p style="color: #0a0">
                      <?php echo base64_decode($_GET['msg']); ?>
                  </p>
                  <?php endif; ?>
                  <br />
                  <div align="center" class="texto">
                    <form action="enviar.php" method="post">
                      <table width="32%"  border="0" align="center">
                        <tr>
                          <td width="33%"><div align="right"><span class="texto">Nome</span></div>
                          </td>
                          <td width="67%"><input name="nome" type="text" id="nome">
                          </td>
                        </tr>
                        <tr>
                          <td><div align="right" class="texto">E-mail:</div>
                          </td>
                          <td><input name="email" type="email">
</td>
                        </tr>
                        <tr>
                          <td><div align="right" class="texto">Assunto</div>
                          </td>
                          <td>
                            <select name="assunto">
                              <option class="form_campos" value="Opinião" selected>Opinião</option>
                              <option class="form_campos" value="Sugestão">Sugestão</option>
                              <option class="form_campos" value="Parceria">Parceria</option>
                              <option class="form_campos" value="Reclamação">Reclamação</option>
                              <option class="form_campos" value="Outros">Outros</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td><div align="right" class="texto">Mensagem</div>
                          </td>
                          <td><textarea name="mensagem" cols="50" rows="10"></textarea>
                          </td>
                        </tr>
                        <tr>
                          <td> </td>
                          <td><input type="submit" name="Submit" value="Enviar">&nbsp;
                            <input type="reset" name="Submit" value="Limpar">
                          </td>
                        </tr>
                      </table>
                    </form>
                  </div>
                </td>
              </tr>
            </table>
            <!-- Fim do conteudo -->
          </td>
        </tr>
        <tr>
          <td height="10" background="images/meio_03.gif"></td>
        </tr>
      </table>
    </td>
    <td width="150" valign="top"><!-- Inicio da busca no Google -->
      <table width="140" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="10" background="images/quad_01.gif"></td>
        </tr>
        <tr>
          <td background="images/quad_02.gif"><table width="128" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td colspan="2"> </td>
              </tr>
              <tr valign="top">
                <td height="18" colspan="2">
                  <div align="center" class="subtitulo"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Buscar
                        na Web por:</strong></font></div>
                </td>
              </tr>
              <FORM method=GET action="http://www.google.com/search" target="_blank">
                <tr>
                  <td width="104"><INPUT TYPE=text name=q size=17 maxlength=255 value="" class="form">
                  </td>
                  <INPUT TYPE=hidden name=hl value="en">
                  <td width="24"><input type=image name=btnG src="images/ok.jpg" border="0">
                  </td>
                </tr>
              </FORM>
              <tr valign="bottom">
                <td height="18" colspan="2">
                  <div align="center"><font color="#ACACAC" size="1" face="Verdana, Arial, Helvetica, sans-serif">Powered
                      by </font><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><a href="http://www.google.com.br" target="_blank">Google</a></font></div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="10" background="images/quad_03.gif"></td>
        </tr>
      </table>
      <!-- Fim da busca no Google -->
      <!-- Inicio da publicidade -->
      <table width="140" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="5"></td>
        </tr>
        <tr>
          <td height="10" background="images/quad_01.gif"></td>
        </tr>
        <tr>
          <td height="18" valign="top" background="images/quad_02.gif">
            <div align="center" class="subtitulo"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Publicidade:</font></strong></div>
          </td>
        </tr>
        <tr>
          <td height="70" background="images/quad_02.gif"><table>
              <tr>
                <td><div align="center" class="texto"> </div>                  <div align="center"></div>
                  <div align="center">                  </div>                  <div align="center"></div>                  <div align="center"></div>                  <div align="center"></div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="10" background="images/quad_03.gif"></td>
        </tr>
      </table>
      <!-- Fim da publicidade -->
    </td>
  </tr>
  <tr>
    <td height="5"></td>
    <td height="5"></td>
    <td height="5"></td>
  </tr>
  <tr>
    <td colspan="3"><!-- Inicio do rodape -->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="6" background="images/bg_03.jpg"></td>
        </tr>
        <tr>
          <td height="24" background="images/bg_04.jpg"><div align="center"><font color="#EBEBEB" size="1" face="Verdana, Arial, Helvetica, sans-serif"><strong>Happy
                  Maple</strong> -
                <a href="http://lithharbor.net">lithharbor.net</a> - Desenvolvido e mantido por <strong>Victor
                Ot&aacute;vio</strong></font></div>
          </td>
        </tr>
      </table>
      <!-- Fim do rodape -->
    </td>
  </tr>
</table>
</body>
</html>
