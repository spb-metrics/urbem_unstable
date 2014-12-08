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
* Manutneção de usuários
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27375 $
$Name$
$Author: trac $
$Date: 2008-01-04 15:31:35 -0200 (Sex, 04 Jan 2008) $

Casos de uso: uc-01.03.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_ADM_NEGOCIO."RUsuario.class.php" );

$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos')."&stAcao=".$stAcao;
$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterSenha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRUsuario = new RUsuario;
switch ($stAcao) {
    case "alterar":
         if ( !strstr( $_POST['stSenha'] , " " ) ) {
            if ($_POST['stSenha'] == $_POST['stConfirmacaoSenha']) {
                $obRUsuario->setUsername                                               ( $_POST['stUserName'] );
                $obRUsuario->setPassword                                               ( $_POST['stSenha'] );
                $obErro = $obRUsuario->alterarSenha( $_POST['stSenhaAtual'] );
            } else {
                $obErro = new Erro;
                $obErro->setDescricao( "A confirmação de senha está errada!" );
            }
        } else {
            $obErro = new Erro;
            $obErro->setDescricao( "A nova senha não pode conter espaços, utilize caracteres do Alfabeto e Números" );
        }
         if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Usuário ".$_POST['stUserName'],"alterar","aviso", Sessao::getId(), "../");
         } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
         }
    break;
}
?>
