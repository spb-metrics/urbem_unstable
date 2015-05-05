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
    * Data de Criação   : 30/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGReceitaCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGReceitaCorrente()
{
    parent::Persistente();

   $this->setTabela('stn.receitaCorrente');

   $this->AddCampo('stExercicio'            , 'varchar', false, '', false, false);
   $this->AddCampo('stFiltro'               , 'varchar', false, '', false, false);
   $this->AddCampo('dtInicial'              , 'varchar', false, '', false, false);
   $this->AddCampo('dtFinal'                , 'varchar', false, '', false, false);
   $this->AddCampo('stCodEntidades'         , 'varchar', false, '', false, false);
   $this->AddCampo('stCodEstruturalInicial' , 'varchar', false, '', false, false);
   $this->AddCampo('stCodEstruturalFinal'   , 'varchar', false, '', false, false);
   $this->AddCampo('stCodReduzidoInicial'   , 'varchar', false, '', false, false);
   $this->AddCampo('stCodReduzidoFinal'     , 'varchar', false, '', false, false);
   $this->AddCampo('inCodRecurso'           , 'varchar', false, '', false, false);
   $this->AddCampo('stDestinacaoRecurso'    , 'varchar', false, '', false, false);
   $this->AddCampo('inCodDetalhamento'      , 'varchar', false, '', false, false);

}

function montaRecuperaTodos()
{
    $stSql  = "
        SELECT *
          FROM ".$this->getTabela()."( '".$this->getDado("stExercicio")."'
                                     , '".$this->getDado("stFiltro")."'
                                     , '".$this->getDado("dtInicial")."'
                                     , '".$this->getDado("dtFinal")."'
                                     , '".$this->getDado("stCodEntidades")."'
                                     , '".$this->getDado("stCodEstruturalInicial")."'
                                     , '".$this->getDado("stCodEstruturalFinal")."'
                                     , '".$this->getDado("stCodReduzidoInicial")."'
                                     , '".$this->getDado("stCodReduzidoFinal")."'
                                     , '".$this->getDado("inCodRecurso")."'
                                     , '".$this->getDado("stDestinacaoRecurso")."'
                                     , '".$this->getDado("inCodDetalhamento")."'
                                     ) AS retorno(
                                                  cod_estrutural      varchar,
                                                  receita             integer,
                                                  recurso             varchar,
                                                  descricao           varchar,
                                                  valor_previsto      numeric,
                                                  arrecadado_periodo  numeric,
                                                  arrecadado_ano      numeric,
                                                  diferenca           numeric
                                     )
                                             ORDER BY cod_estrutural    ";

    return $stSql;
}

}
