<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00, uc-03.01.06
*/

//include( "../sistema/setup.inc.php"          );
//include( "tabelas.inc.php"                   );
//include( "views.inc.php"                     );
//include( "../classes/sessao.class.php"       );
//include( "../classes/dataBase.class.php"     );
//include( "../bibliotecas/funcoes.lib.php"    );
//include( "../classes/paginacao.class.php"    );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';

if (isset($acao)) {
    Sessao::write('acao', $acao);
}

$sSQL = "SELECT nom_acao FROM administracao.acao WHERE cod_acao =".Sessao::read('acao');
$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$gera="";
while (!$dbEmp->eof()) {
   $nomeacao  = trim($dbEmp->pegaCampo("nom_acao"));
   $dbEmp->vaiProximo();
   $gera .= $nomeacao;
}
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
?>

<html>
<head>
<script language="JavaScript1.2" type="text/javascript">
    function alertaAviso(objeto,tipo,chamada)
    {
        var x = 350;
        var y = 200;
        var sessao   = '<?=Sessao::getId()?>';
        var sessaoid = sessao.substr(10,6);
        var sArq = 'alerta.inc.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
        var sAux = "msga"+ sessaoid +" = window.open(sArq,'msga"+ sessaoid +"','width=350px,height=250px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
        eval(sAux);
    }
</script>

<link rel=STYLESHEET type=text/css href=stylos_ns.css>

<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'>
<meta http-eqiv='Expires' content='10 mar 1967 09:00:00 GMT'>
</head>

<body leftmargin=0 topmargin=0>
<table width="100%" align="center">
<tr>
    <td align="center">
    <table width=100%>
    <tr>
        <td class="labelcenter" height=5 width=100%><font size=1 color=#535453><b>&raquo; <?=$gera?></b></font></td>
    </tr>
    </table>

<?php

if (!(isset($ctrl))) {
    $ctrl = 0;
}
switch ($ctrl) {
    case 0:
?>

    <script language="JavaScript1.2" type="text/javascript">
        function Valida()
        {
        var mensagem = "";
        var erro = false;
        var campo;
        var campo2;
        var campo3;
        var campo4;
        var campo5;
        var campoaux;

        campo = document.frm.codGrupo.value;
        campo2 = document.frm.codNatureza.value;
        campo3 = document.frm.codEspecie.value;
        campo4 = document.frm.atributo.value.length;
        campo5 = document.frm.descricao.value.length;
        campo6 = document.frm.placa.value.length;
        if ((campo == "xxx") && (campo2 == "xxx") && (campo3 == "xxx") && (campo4 == 0) && (campo5 == 0) && (campo6 == 0)) {
            mensagem += "@Escolha ao menos uma Opção";
            erro = true;
        }
        if (erro) {
            alertaAviso(mensagem,'form','erro');
        }

        return !(erro);
        }

        function Salvar()
        {
            if (Valida()) {
            document.frm.action = "procuraBem.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
            }
        }
    </script>

    <form name="frm" action="procuraBem.php?<?=Sessao::getId()?>" method="POST">

    <table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Dados para Bem</td>
    </tr>
    <tr>
        <td class="label" width="30%">Natureza</td>
        <td class="field">
            <select name="codNatureza" onChange="document.frm.submit();" style="width:200px">
                <option value="xxx">Todos</option>
<?php
                $sSQL = "SELECT * FROM patrimonio.natureza ORDER by nom_natureza";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboNatureza = "";
                while (!$dbEmp->eof()) {
                    $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                    $nomNatureza  = trim($dbEmp->pegaCampo("nom_natureza"));
                    $dbEmp->vaiProximo();
                    $comboNatureza .= "<option value=".$codNaturezaf;
                            if (isset($codNatureza)) {
                                if ($codNaturezaf == $codNatureza)
                                $comboNatureza .= " SELECTED";
                            }
                    $comboNatureza .= ">".$nomNatureza."</option>\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
                echo $comboNatureza;

?>
            </select>
        </td>
    </tr>

    <tr>
        <td class="label">Grupo</td>
        <td class="field">
            <select name="codGrupo" onChange="document.frm.submit();" style="width:200px">
                <option value="xxx" SELECTED>Todos</option>
<?php
                if ((!(isset($codNatureza))) OR ($codNatureza == "xxx")) {

                } else {
                    $sSQL = "SELECT * FROM patrimonio.grupo WHERE cod_natureza = ".$codNatureza." ORDER by nom_grupo";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                    $comboGrupo = "";
                    while (!$dbEmp->eof()) {
                        $codGrupof  = trim($dbEmp->pegaCampo("cod_grupo"));
                        $nomGrupo  = trim($dbEmp->pegaCampo("nom_grupo"));
                        $dbEmp->vaiProximo();
                        $comboGrupo .= "<option value=".$codGrupof;
                                if (isset($codGrupo)) {
                                    if ($codGrupof == $codGrupo)
                                    $comboGrupo .= " SELECTED";
                                }
                        $comboGrupo .= ">".$nomGrupo."</option>\n";
                        }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboGrupo;
                }
?>
            </select>
        </td>
    </tr>

    <tr>
        <td class="label">Espécie</td>
        <td class="field">
            <select name="codEspecie" onChange="document.frm.submit();" style="width:200px">
                <option value="xxx" SELECTED>Todos</option>
<?php
                if ((!(isset($codGrupo))) OR ($codGrupo == "xxx")) {

                } else {
                    $sSQL = "SELECT * FROM patrimonio.especie WHERE cod_grupo = ".$codGrupo." AND cod_natureza = ".$codNatureza." ORDER by nom_especie";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();
                    $comboEspecie = "";
                    while (!$dbEmp->eof()) {
                        $codEspecief  = trim($dbEmp->pegaCampo("cod_especie"));
                        $nomEspecief  = trim($dbEmp->pegaCampo("nom_especie"));
                        $dbEmp->vaiProximo();
                        $comboEspecie .= "<option value=".$codEspecief;
                                if (isset($codEspecie)) {
                                    if ($codEspecief == $codEspecie)
                                    $comboEspecie .= " SELECTED";
                                }
                        $comboEspecie .= ">".$nomEspecief."</option>\n";
                        }
                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboEspecie;
                }
?>
            </select>
        </td>
    </tr>

    <tr>
        <td class="label">Descrição</td>
        <td class="field">
            <input type="text" name="descricao" size=60>
        </td>
    </tr>

    <tr>
        <td class="label">Atributo</td>
        <td class="field">
            <input type="text" name="atributo" size=60>
            <input type="hidden" name="campoBem" value="<?=$campoBem;?>">
            <input type="hidden" name="nomForm" value="<?=$nomForm;?>">
        </td>
    </tr>
    <tr>
       <td class="label">Placa de Identificação</td>
       <td class="field">
           <input type="text" name="placa" size=10>
       </td>
    </tr>

    <tr>
        <td class="label">Ordenar por</td>
        <td class="field">
            <select name="ordem" style="width:200px">
                <option value="e.nom_especie" SELECTED>Espécie</option>
                <option value="b.descricao">Descrição</option>
                <option value="b.cod_bem">Código do Bem</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="field" colspan=2>
            <input type="button" value="OK" style="width: 60px;" onClick="Salvar();">&nbsp;
            <input type="reset" value="Limpar" style="width: 60px;">
        </td>
    </tr>
    </table>
</form>

<?php
    break;

    case 1:

    while ( list( $key, $val ) = each( $HTTP_POST_VARS ) ) {
        $variavel = $key;
        $$variavel = $val;
        $aVarWhere[$key] = $val;
    }

    function MontaWhere()
    {
    global $aVarWhere;

    $i=0;
    while (list($key,$val) = each($aVarWhere)) {
        $variavel = $key;
        $$variavel = $val;

        if ( $val <> "xxx" AND trim($val) <> "" ) {
            switch ($key) {
                case "codGrupo" :
                $sCampo="b.cod_grupo";
                $aCampos[$i] = array($sCampo,"N",$val,"=");
                $i++;
                break;
                case "codNatureza" :
                $sCampo="b.cod_natureza";
                $aCampos[$i] = array($sCampo,"N",$val,"=");
                $i++;
                break;
                case "codEspecie" :
                $sCampo="b.cod_especie";
                $aCampos[$i] = array($sCampo,"N",$val,"=");
                $i++;
                break;
            }
        }
    }
    $i=0;
    $sWhere = "";
    while ($i<sizeof($aCampos)) {
        if ($aCampos[$i][1]=="T") {
            if ($aCampos[$i][3]=="L") {
                $sParte = $aCampos[$i][0]." like '%".$aCampos[$i][2]."%'";
            } else {
                $sParte = $aCampos[$i][0].$aCampos[$i][3]."'".$aCampos[$i][2]."'";
            }
        } else {
            $sParte = $aCampos[$i][0].$aCampos[$i][3].$aCampos[$i][2];
        }
        if (strlen($sWhere)>0) {
            $sWhere = $sWhere." and ".$sParte;
        } else {
            $sWhere = $sParte;
        }
        $i++;
    }
        if (strlen($sWhere)>0) {
            $sWhere = " AND ".$sWhere;
        }

    return $sWhere;
    }

    $sWhere = MontaWhere();

    //monta relacao para bens que possuam atributos
    if ($atributo != "") {
        $tbAtributos = "patrimonio.bem_atributo_especie as bae,";

        $whAtributos  = " AND bae.cod_especie = e.cod_especie";
        $whAtributos .= " AND bae.cod_grupo = e.cod_grupo";
        $whAtributos .= " AND bae.cod_natureza = e.cod_natureza";
        $whAtributos .= " AND bae.cod_grupo = g.cod_grupo";
        $whAtributos .= " AND bae.cod_natureza = g.cod_natureza";
        $whAtributos .= " AND bae.cod_natureza = n.cod_natureza";
    }

    $sSQLs  = "SELECT b.*                              \n";
    $sSQLs .= "     , e.nom_especie                    \n";
    $sSQLs .= "FROM                                    \n";
    $sSQLs .= $tbAtributos                           ."\n";
    $sSQLs .= "     patrimonio.grupo    as g       \n";
    $sSQLs .= "   , patrimonio.natureza as n                  \n";
    $sSQLs .= "   , patrimonio.especie  as e                  \n";
    $sSQLs .= "   , patrimonio.bem      as b                  \n";
    $sSQLs .= "    LEFT JOIN patrimonio.bem_baixado as bb ON (\n";
    $sSQLs .= "        b.cod_bem = bb.cod_bem          \n";
    $sSQLs .= "    )                                   \n";
    $sSQLs .= "WHERE                                   \n";
    $sSQLs .= "  coalesce(bb.cod_bem,0)  <=0           \n";
    $sSQLs .= "  -- Join com sw_especie                \n";
    $sSQLs .= "  AND e.cod_especie  = b.cod_especie    \n";
    $sSQLs .= "  AND e.cod_natureza = b.cod_natureza   \n";
    $sSQLs .= "  AND e.cod_grupo    = b.cod_grupo      \n";
    $sSQLs .= "  -- Join com sw_grupo                  \n";
    $sSQLs .= "  AND g.cod_natureza = e.cod_natureza   \n";
    $sSQLs .= "  AND g.cod_grupo    = e.cod_grupo      \n";
    $sSQLs .= "  -- Join com sw_natureza               \n";
    $sSQLs .= "  AND n.cod_natureza = g.cod_natureza   \n";
    $sSQLs .= "  -- filtros                            \n";
    $sSQLs .= $whAtributos.$sWhere;

    if ($atributo != "") {
        $sSQLs .= " AND lower(bae.valor_atributo) LIKE lower('%".$atributo."%')" ;
    }
    if ($descricao != "") {
        $sSQLs .= " AND lower(b.descricao) LIKE lower('%".$descricao."%')" ;
    }
    if ($placa != "") {
        $sSQLs .= " AND b.num_placa = '".$placa."'" ;
    }
    $sSQLs .= " ORDER by ".$ordem;
?>
    <script language="JavaScript1.2" type="text/javascript">
        function Insere(num)
        {
            var sNum;
            sNum = num;
            window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.<?=$campoBem;?>.value = sNum;
            window.close();

        }

        function InsereBem(num1,num2,num3)
        {
            var sNum;
            var conta = 0;
            sNum1     = num1;
            sNum2     = num2;

            if (window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.numPlaca) {
                window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.numPlaca.value = sNum1;
            }

            if (typeof(num3)!='undefined') {
               if (window.opener.parent.frames['telaPrincipal'].document.getElementById("lblDescricaoBem")) {

                     campo = decodeURIComponent(num3);
                     var valor = new String();
                     while (conta<campo.length) {
                       valor+= campo[conta];
                         if (valor.charCodeAt(conta)==43) {
                            valor = valor.replace(valor.charAt(conta)," ");
                         }
                       conta++;
                     }
                   window.opener.parent.frames['telaPrincipal'].document.getElementById("lblDescricaoBem").innerHTML = valor;

               }
            }
            window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.codBem.value = sNum2;

            window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.codBem.focus();

            try {
              window.opener.parent.frames['telaPrincipal'].testeEnvia();
            } finally {
              window.close();
            }

        }

        </script>

        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="5">Registros de Bem</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%">&nbsp;</td>
            <td class="labelcenter" width="5%">Código</td>
            <td class="labelcenter" width="5%">Placa de Identificação</td>
            <td class="labelcenter" width="30%">Espécie</td>
            <td class="labelcenter" width="55%">Descrição</td>
            <td class="labelcenter" width="5%">&nbsp;</td>
        </tr>
<?php
        //print $sSQL;

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados( $sSQLs, "10" );
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("b.cod_bem","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQLs);
        $dbEmp->vaiPrimeiro();
        $cont = $paginacao->contador();
        while (!$dbEmp->eof()) {
            $codBem  = trim($dbEmp->pegaCampo("cod_bem"));
            $numPlaca  = trim($dbEmp->pegaCampo("num_placa"));
            $nomEspecie  = trim($dbEmp->pegaCampo("nom_especie"));
            $descricao  = trim($dbEmp->pegaCampo("descricao"));

            $dbEmp->vaiProximo();
?>
            <tr>
                <td class="labelcenter"><?=$cont++;?></td>
                <td class="show_dados_right"><?=$codBem;?></a></td>
                <td class="show_dados"><?=$numPlaca;?></a></td>
                <td class="show_dados"><?=$nomEspecie;?></a></td>
                <td class="show_dados"><?=$descricao;?></a></td>
                <td class="show_dados"><a href='#' onClick="InsereBem('<?=$numPlaca?>','<?=$codBem;?>','<?=urlencode($descricao)?>');">Selecionar</a></td>
<!--                <td class="show_dados"><a href='#' onClick="InsereBem('<?=$numPlaca?>','<?=$codBem;?>','<?=$descricao;?>');">Selecionar</a></td> -->
            </tr>
<?php
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
?>
    </table>
        <input type="button" value="Voltar" onClick="javascript:document.location.replace('procuraBem.php?<?=Sessao::getId()?>&nomForm=<?=$nomForm;?>&campoBem=<?=$campoBem;?>');">&nbsp;
        <input type="button" value="Fechar" onClick="javascript:window.close();">
<?php
    break;
}

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
