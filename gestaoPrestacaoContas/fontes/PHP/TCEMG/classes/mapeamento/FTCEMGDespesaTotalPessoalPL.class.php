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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 20/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaTotalPessoalPL extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDespesaTotalPessoalPL()
{
    parent::Persistente();
}

function montaRecuperaRelacionamento()
{
    $stSql = "select stn.fn_rgf_despesa_liquidada_anexo1('".$this->getDado("dt_inicial")."'
                                                       , '".$this->getDado("dt_final")."'
                                                       , '".$this->getDado("cod_entidade")."'
                                                       , '".$this->getDado("filtro")."') as valor";

    return $stSql;
}

}
