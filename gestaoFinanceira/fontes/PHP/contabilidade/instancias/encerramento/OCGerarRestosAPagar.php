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
    * Página de Formulário - Gerar Restos a Pagar

    * Data de Criação   : 20/12/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-12-27 19:23:46 -0200 (Qua, 27 Dez 2006) $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function verificaEntidades($arCodEntidade)
{
    foreach ($arCodEntidade as $array) {
        if ($array == $_REQUEST['inCodEntidadeCredito']) {
            $stJs .= "jq('#lblObs').html('Este processo já foi executado! Se deseja prosseguir faça a Anulação de Restos à pagar primeiro!');\n
                      jq('#Ok').attr('disabled',true);\n";
        } else {
            $stJs .= "jq('#lblObs').html('Este processo é lento devido aos cálculos de restos a pagar.<BR>Recomenda-se que o mesmo seja executado após o término do expediente.');\n
                      jq('#Ok').attr('disabled',false);\n";
        }
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case 'verificaEntidade':
        $stJs = verificaEntidades(Sessao::read('arCodEntidade'));
        echo $stJs;

    break;
}

?>
