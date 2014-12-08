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
    * Arquivo de mapeamento para a função que busca os dados  de despesa do pessoal
    * Data de Criação   : 22/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaTotalPessoalPE extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDespesaTotalPessoalPE()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_despesa_total_pessoal_pe');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
 $stSql  = "
        SELECT mes
             , vencVantagens
             , inativos
             , pensionistas
             , salarioFamilia
             , subsPrefeito
             , subsVice
             , subsSecret
             , obrigPatronais
             , repassePatronal
             , sentJudPessoal
             , indenDemissao
             , incDemVolunt
             , sentJudAnt
             , inatPensFontCustProp
             , outrasDespesasPessoal
             , nadaDeclararPessoal
             , despAnteriores
             , exclusaoDespAnteriores
          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'
                                     , ".$this->getDado("mes")."
                                     ) AS retorno(
                                                  mes              INTEGER,
                                                  vencVantagens    NUMERIC(14,2),
                                                  inativos         NUMERIC(14,2),
                                                  pensionistas     NUMERIC(14,2),
                                                  salarioFamilia   NUMERIC(14,2),
                                                  subsPrefeito     NUMERIC(14,2),
                                                  subsVice         NUMERIC(14,2),
                                                  subsSecret       NUMERIC(14,2),
                                                  obrigPatronais   NUMERIC(14,2),
                                                  repassePatronal  NUMERIC(14,2),
                                                  sentJudPessoal   NUMERIC(14,2),
                                                  indenDemissao    NUMERIC(14,2),
                                                  incDemVolunt     NUMERIC(14,2),
                                                  sentJudAnt       NUMERIC(14,2),
                                                  inatPensFontCustProp   NUMERIC(14,2),
                                                  outrasDespesasPessoal  NUMERIC(14,2),
                                                  despAnteriores         NUMERIC(14,2),
                                                  exclusaoDespAnteriores NUMERIC(14,2),
                                                  nadaDeclararPessoal    VARCHAR(1)
                                                 )";

    return $stSql;
     }
   }
