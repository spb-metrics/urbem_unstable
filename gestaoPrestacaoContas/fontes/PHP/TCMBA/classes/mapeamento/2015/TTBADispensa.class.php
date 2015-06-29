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
    * Extensão da Classe de mapeamento
    * Data de Criação: 15/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: diego $
    $Date: 2007-10-16 01:38:47 +0000 (Ter, 16 Out 2007) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/10/16 01:38:47  diego
Arquivos novos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBADispensa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTBADispensa()
    {
        $this->setEstrutura         ( array() );
        $this->setEstruturaAuxiliar ( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stCondicao = '';

        $stCondicao = " where compra_direta.exercicio_mapa = '" . $this->getDado ( 'exercicio' ) . "' ";

        if ( $this->getDado ( 'inMes') ) {
            $stCondicao .= " and extract ( month from ( compra_direta.timestamp ) ) = " . $this->getDado( 'inMes' ) ;
        }
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stCondicao .= " and compra_direta.cod_entidade in ( " . $this->getDado ( 'stEntidades' ) . " ) ";
        }

        $stSql = "select 1 as tipo_registro
                       , compra_direta.cod_compra_direta
                       , compra_direta.cod_entidade
                       , objeto.descricao as objeto
                       , configuracao_entidade.valor as unidade_gestora
                       , sum ( mapa_item.vl_total ) as valor
                       , extract ( year from compra_direta.timestamp )|| extract ( month from compra_direta.timestamp )   as competencia
                       , case when ( compra_direta.cod_modalidade = 5 )
                              then 5
                              else 6
                         end as cod_modalidade
                    from compras.compra_direta
                    join compras.objeto
                      on ( compra_direta.cod_objeto = objeto.cod_objeto )
                    join administracao.configuracao_entidade
                      on ( configuracao_entidade.exercicio     = compra_direta.exercicio_entidade
                     and   configuracao_entidade.cod_entidade  = compra_direta.cod_entidade
                     and   configuracao_entidade.parametro     = 'tcm_unidade_gestora' )
                    join compras.mapa_item
                      on ( compra_direta.cod_mapa = mapa_item.cod_mapa
                     and   compra_direta.exercicio_mapa = mapa_item.exercicio )
                        $stCondicao
                  group by compra_direta.cod_compra_direta
                         , compra_direta.cod_entidade
                         , objeto
                         , unidade_gestora
                         , competencia
                         , cod_modalidade
                 ";

        return $stSql;
    }
}
